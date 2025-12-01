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
        // Map contest IDs to their CSV files
        $csvFiles = [
            2 => public_path('contest_2_users.csv'),
            // Add more mappings as needed
        ];

        if (!isset($csvFiles[$contestId]) || !file_exists($csvFiles[$contestId])) {
            return null;
        }

        $csvFile = $csvFiles[$contestId];
        
        if (($handle = fopen($csvFile, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== false) {
                $row = array_combine($headers, $data);
                
                // Check if this row matches the student ID (check all clan columns)
                if (isset($row['Clan_1']) && $row['Clan_1'] === $studentId) {
                    fclose($handle);
                    return [
                        'username' => $row['Username'] ?? null,
                        'password' => $row['Password'] ?? null,
                    ];
                }
            }
            
            fclose($handle);
        }
        
        return null;
    }
}
