<?php

namespace App\Livewire;

use App\Models\Submission;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class IncentiveForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Please provide your personal details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->placeholder('Enter your full name')
                            ->required(),

                        TextInput::make('student_id')
                            ->label('Student ID')
                            ->placeholder('Enter your student ID')
                            ->required(),

                        TextInput::make('batch')
                            ->label('Batch')
                            ->placeholder('CSE 65')
                            ->required(),


                        TextInput::make('email')
                            ->default(auth()->user()?->email)
                            ->suffixIcon('heroicon-o-at-symbol')
                            ->disabled()
                            ->required(),

                        TextInput::make('semester')
                            ->label('Current Semester')
                            ->placeholder('e.g. Fall 2025')
                            ->required(),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->length(11)
                            ->placeholder('Enter your contact number')
                            ->required(),

                        // TextInput::make('total_solved')
                        //     ->label('Total Solved Problems (This Semester only)')
                        //     ->numeric()
                        //     ->required(),


                        // TextInput::make('tracker_url')
                        //     ->label('Personal Tracker URl')
                        //     ->required(),
                    ])
                    ->columns(2),

                Section::make('Course Information')
                    ->description('Add the courses you want to apply for')
                    ->schema([
                        Repeater::make('courses')
                            ->label('Courses')
                            ->schema([
                                TextInput::make('teacher_name')
                                    ->label('Teacher Name')
                                    ->placeholder('Full name of the teacher')
                                    ->required(),

                                TextInput::make('teacher_initial')
                                    ->label('Teacher Initial')
                                    ->placeholder('e.g. ABC')
                                    ->required(),
                                TextInput::make('section')
                                    ->label('Section')
                                    ->placeholder('e.g. A')
                                    ->required(),
                                TextInput::make('teacher_mail')
                                    ->label('Teacher Email')
                                    ->email()
                                    ->endsWith(['@diu.edu.bd', '@daffodilvarsity.edu.bd'])
                                    ->placeholder('e.g. abc@diu.edu.bd')
                                    ->required(),
                                TextInput::make('teacher_phone')
                                    ->label('Teacher Phone')
                                    ->tel()
                                    ->length(11)
                                    ->placeholder('e.g. 017XXXXXXXX')
                                    ->required(),

                                TextInput::make('course_name')
                                    ->label('Course Name')
                                    ->placeholder('Full name of the course')
                                    ->required(),


                                TextInput::make('course_code')
                                    ->label('Course Code')
                                    ->placeholder('e.g. CSE101')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['course_name'] ?? null)
                            ->addActionLabel('Add Another Course')
                            ->defaultItems(1)
                    ])
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        Submission::create(array_merge($data, [
            'email' => auth()->user()?->email,
        ]));

        $this->form->fill();

        Notification::make()
            ->title("Application Submitted Successfully")
            ->body("Thank you for submitting your incentive application.")
            ->success()
            ->duration(5000)
            ->send();
    }

    public function render()
    {
        return view('livewire.incentive-form');
    }
}
