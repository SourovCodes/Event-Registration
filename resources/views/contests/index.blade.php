i<x-web-layout>
    <section class="dark:bg-gray-900">
        <div class="container mx-auto mt-5 space-y-5 px-2">
            <!-- Card Blog -->
            <div class="container mx-auto px-2 py-10">


                <!-- Grid -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($contests as $contest)
                        <!-- Card -->
                        <a class="" href="{{ route("contests.show", $contest->slug) }}">
                            <x-card class="group flex flex-col h-full justify-between">
                                <div>
                                    <div class="aspect-w-16 aspect-h-9">
                                        {{ $contest->getFirstMedia("contest-banner-images") ?->img()->attributes(["class" => "w-full object-cover rounded-xl"]) }}
                                    </div>
                                    <div class="my-6">
                                        <h3
                                            class="line-clamp-2 text-xl font-semibold text-gray-800 dark:text-neutral-300 dark:group-hover:text-white">
                                            {{ $contest->name }}
                                        </h3>
                                        <p
                                            class="mt-5 text-gray-600 dark:text-neutral-400">
                                            {!! Str::limit(strip_tags($contest->description)) !!}
                                        </p>
                                    </div>
                                </div>

                                <x-button.primary>
                                    Register Now
                                </x-button.primary>
                            </x-card>
                        </a>
                        <!-- End Card -->
                    @endforeach
                </div>
                <!-- End Grid -->

                <div class="mt-2">
                    {{$contests->links()}}
                </div>
            </div>
            <!-- End Card Blog -->
        </div>
    </section>
</x-web-layout>
