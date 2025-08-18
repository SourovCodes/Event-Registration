<x-web-layout>
    {{-- <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header with gradient background -->
                <div class="text-center mb-10 bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-800 dark:to-indigo-900 p-10 rounded-lg shadow-lg">
                    <h1 class="text-4xl font-bold text-white mb-3">Incentive Application</h1>
                    <p class="text-lg text-gray-100">
                        Complete this form to submit your course incentive application
                    </p>
                </div>

                <!-- Form Card with improved styling -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700 transition-all duration-300">
                    <div class="p-8">
                        <livewire:incentive-form/>
                    </div>
                </div>

                <!-- Additional Information with dark mode support -->
                <div class="mt-10 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Important Information</h3>
                    <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-3">
                        <li>Make sure to provide accurate teacher information</li>
                        <li>All fields are required to process your application</li>
                        <li>Your performance and participation info will be collected from <a class="text-blue-500" href="https://diuacm.com">diuacm.com</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Deadline Over Notice -->
                <div class="text-center mb-10 bg-gradient-to-r from-red-600 to-red-700 dark:from-red-800 dark:to-red-900 p-10 rounded-lg shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-red-100 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h1 class="text-4xl font-bold text-white mb-3">Application Deadline Expired</h1>
                    <p class="text-xl text-gray-100 mb-4">
                        The incentive application submission period has ended.
                    </p>
                    <p class="text-md text-gray-200">
                        If you have any questions or believe this is an error, please contact the administration.
                    </p>
                </div>
                
               
            </div>
        </div>
    </div>
</x-web-layout>
