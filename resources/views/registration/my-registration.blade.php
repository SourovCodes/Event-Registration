<x-web-layout>
    <div class="container mx-auto px-4 py-8 md:py-16 max-w-4xl">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="relative">
                <div class="aspect-w-16 aspect-h-5 max-h-72">
                    {{ $registration->contest->getFirstMedia("contest-banner-images") ?->img()->attributes(["class" => "w-full h-full object-cover"]) }}
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6">
                    <span class="inline-block px-3 py-1 mb-3 text-xs font-medium tracking-wider uppercase bg-indigo-600 bg-opacity-85 text-white rounded-full">Registration</span>
                    <h2 class="text-white font-bold text-2xl md:text-3xl drop-shadow-sm">{{ $registration->contest->name }}</h2>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white">Registration Details</h3>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                        bg-{{ $registration->status->getColor() }}-100/70
                        text-{{ $registration->status->getColor() }}-700
                        dark:bg-{{ $registration->status->getColor() }}-900/30
                        dark:text-{{ $registration->status->getColor() }}-400
                        shadow-sm">
                        <span class="w-2 h-2 mr-2 rounded-full bg-{{ $registration->status->getColor() }}-500"></span>
                        {{ $registration->status }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div class="flex items-center gap-3 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h4>
                        </div>

                        <div class="space-y-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->name }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->email }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->phone }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Student ID</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->student_id }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Gender</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->gender }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="flex items-center gap-3 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Information</h4>
                        </div>

                        <div class="space-y-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Department</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->department }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Section</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->section }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Lab Teacher</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->lab_teacher_name }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">T-Shirt Size</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->tshirt_size }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($credentials)
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Contest Credentials</h4>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-5 border border-green-100 dark:border-green-800/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Username</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-semibold text-lg text-gray-900 dark:text-white">{{ $credentials['username'] }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $credentials['username'] }}')" 
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                            title="Copy username">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Password</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-semibold text-lg text-gray-900 dark:text-white">{{ $credentials['password'] }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $credentials['password'] }}')" 
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                            title="Copy password">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-center">
                            <a href="https://www.judgenot0.com/contests/2" target="_blank" 
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Access Contest Platform
                            </a>
                        </div>

                        <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800/30">
                            <p class="text-xs text-gray-600 dark:text-gray-400 flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Use these credentials to access <strong>DIU Take-off Fall 2025 Practice Contest</strong>. Keep them secure and do not share with others.</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Details</h4>
                    </div>

                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-5 border border-indigo-100 dark:border-indigo-800/30">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Payment Method</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->payment_method }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Phone Number</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->payment_phone }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Transaction ID</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $registration->payment_transaction_id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($registration->status == \App\Enums\RegistrationStatuses::PENDING)
            <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 text-amber-800 dark:text-amber-300 rounded-xl p-5" role="alert">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-amber-800 dark:text-amber-300 font-semibold mb-1">Registration Pending</h4>
                        <p class="text-sm">Your form has been successfully submitted. We're now verifying your payment and will update your registration status soon. This process may take some time as we manually check all details.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/30 text-blue-800 dark:text-blue-300 rounded-xl p-5" role="alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-blue-800 dark:text-blue-300 font-semibold mb-1">Payment Support</h4>
                    <p class="text-sm">For payment related issues, please contact <a href="mailto:didarul15-4679@diu.edu.bd" class="underline font-medium">didarul15-4679@diu.edu.bd</a> (Md. Didarul Islam, CPC Treasurer)</p>
                </div>
            </div>
        </div>
    </div>
</x-web-layout>
