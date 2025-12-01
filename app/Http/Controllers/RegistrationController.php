<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatuses;
use App\Models\Contest;
use App\Models\Registration;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function create(Contest $contest)
    {   

        if($contest->registration_start_time && $contest->registration_start_time > now()) {
            Notification::make()
                ->title("Registration not started yet!")
                ->body("Contest registration will start after " . $contest->registration_start_time->diffForHumans())
                ->warning()
                ->send();
            return redirect(route('contests.show', $contest));
        }

        if ($contest->registration_deadline < now()) {

            Notification::make()
                ->title("You are late!")
                ->body("Contest registration deadline has been passed.")
                ->warning()
                ->send();
            return redirect(route('contests.show', $contest));
        }
        $registration = Registration::where('user_id', auth()->user()->id)->where('contest_id', $contest->id)->first();

        if ($registration?->status == RegistrationStatuses::PAID || $registration?->status == RegistrationStatuses::PENDING) {

            return redirect(route('contests.registration.myRegistration', $contest));
        }
        return view('contests.registration.create', compact('contest'));
    }

    public function myRegistration(Contest $contest)
    {

        $registration = Registration::where('user_id', auth()->user()->id)->where('contest_id', $contest->id)->first();

        if (!$registration) {
            return redirect(route('contests.registration.form', $contest));
        }

        // Find credentials from CSV based on student ID
        $credentials = $this->findCredentialsByStudentId($registration->student_id, $contest->id);

        return view('registration.my-registration', compact('registration', 'credentials'));
    }

    private function findCredentialsByStudentId($studentId, $contestId)
    {
        // Get the contest and check for uploaded CSV
        $contest = Contest::find($contestId);
        
        if (!$contest) {
            return null;
        }
        
        // Try to get CSV from media library first
        $csvMedia = $contest->getFirstMedia('credentials-csv');
        
        if ($csvMedia) {
            $csvFile = $csvMedia->getPath();
        } else {
            // Fallback to hardcoded paths for backward compatibility
            $csvFiles = [
                6 => storage_path('app/contest_6_users.csv'),
                2 => storage_path('app/contest_2_users.csv'),
            ];
            
            if (!isset($csvFiles[$contestId]) || !file_exists($csvFiles[$contestId])) {
                return null;
            }
            
            $csvFile = $csvFiles[$contestId];
        }
        
        if (!file_exists($csvFile)) {
            return null;
        }
        
        if (($handle = fopen($csvFile, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            // Normalize student ID for comparison
            $normalizedStudentId = trim($studentId);
            
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < count($headers)) {
                    continue; // Skip malformed rows
                }
                
                $row = array_combine($headers, $data);
                
                // Match student ID with Clan_1 column
                if (isset($row['Clan_1']) && trim($row['Clan_1']) === $normalizedStudentId) {
                    fclose($handle);
                    return [
                        'username' => isset($row['Username']) ? trim($row['Username']) : null,
                        'password' => isset($row['Password']) ? trim($row['Password']) : null,
                        'name' => isset($row['Name']) ? trim($row['Name']) : null,
                    ];
                }
            }
            
            fclose($handle);
        }
        
        return null;
    }
}
