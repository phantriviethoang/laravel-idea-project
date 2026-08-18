<x-layout>
    <div class="mx-auto max-w-4xl py-8">
        <div class="flex items-center justify-between">
            <a
                href="{{ route('idea.index') }}"
                class="flex items-center gap-x-2 text-sm font-medium"
            >
                <x-icons.arrow-back />
                Back to ideas
            </a>

            <div class="flex items-center gap-x-3">
                <button
                    x-data
                    class="btn btn-outlined"
                    data-test="edit-idea-button"
                    @click="$dispatch('open-modal', 'edit-idea')"
                >
                    <x-icons.external />

                    Edit Idea
                </button>

                <form
                    method="POST"
                    action="{{ route('idea.destroy', $idea) }}"
                >
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-outlined text-red-500">Delete</button>
                </form>
            </div>
        </div>
        <div class="mt-8 space-y-7">
            @if ($idea->image_path)
                <div class="overflow-hidden rounded-lg">
                    <img
                        src="{{ asset('storage/' . $idea->image_path) }}"
                        alt=""
                        class="h-auto w-full object-cover"
                    >
                </div>
            @endif
            <h1 class="text-4xl font-bold">{{ $idea->title }}</h1>

            <div class="mt-2 flex items-center gap-x-3">
                <x-idea.status-label :status="$idea->status->value">{{ $idea->status->label() }}</x-idea.status-label>

                <div class="text-muted-foreground text-sm">{{ $idea->created_at->diffForHumans() }}</div>
            </div>

            @if ($idea->description)
                <x-card class="mt-6" is="div">
                    <div class="text-foreground max-w-none cursor-pointer  prose prose-invert">{!! $idea->formattedDescription !!}</div>
                </x-card>
            @endif

            @if ($idea->steps->count())
                <div>
                    <h3 class="mt-6 text-xl font-bold">Actionable Steps</h3>

                    <div class="mt-3 space-y-2">
                        @foreach ($idea->steps as $step)
                            <x-card>
                                <form
                                    method="POST"
                                    action="{{ route('step.update', $step) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div class="flex items-center gap-x-3">
                                        <button
                                            type="submit"
                                            role="checkbox"
                                            class="text-primary-foreground {{ $step->completed ? 'bg-primary' : 'border border-primary' }} flex size-5 items-center justify-center rounded-lg"
                                        >&check;</button>
                                        <span
                                            class="{{ $step->completed ? 'line-through text-muted-foreground' : '' }}">{{ $step->description }}</span>
                                    </div>
                                </form>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($idea->links->count())
                <div>
                    <h3 class="mt-6 text-xl font-bold">Links</h3>

                    <div class="mt-3 space-y-2">
                        @foreach ($idea->links as $link)
                            <x-card
                                :href="$link"
                                class="text-primary flex items-center gap-x-3 font-medium"
                            >
                                <x-icons.external />
                                {{ $link }}
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <x-idea.modal :idea="$idea" />
    </div>
</x-layout>
