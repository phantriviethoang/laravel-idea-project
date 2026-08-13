<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground mt-2 text-sm">Capture your thoughts. Make a plan.</p>

            <x-card
                x-data
                @click="$dispatch('open-modal', 'create-idea')"
                is="button"
                type="submit"
                data-test="create-idea-button"
                class="mt-10 h-32 w-full cursor-pointer text-left"
            >
                What's the idea?
            </x-card>

        </header>

        <div>
            <a
                href="/ideas"
                class="btn {{ request()->has('status') ? 'btn-outlined' : '' }}"
            >All</a>

            @foreach (App\IdeaStatus::cases() as $status)
                <a
                    href="/ideas?status={{ $status->value }}"
                    class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}"
                >
                    {{ $status->label() }} <span class="tex-xs pl-3">{{ $statusCounts->get($status->value) }}</span>
                </a>
            @endforeach
        </div>

        <div class="text-muted-foreground mt-10">
            <div class="grid gap-6 md:grid-cols-2">
                @forelse ($ideas as $idea)
                    <x-card href="{{ route('idea.show', $idea) }}">
                        @if ($idea->image_path)
                            <div class="-mx-4 mb-4 overflow-hidden rounded-t-lg">
                                <img
                                    src="{{ asset('storage/' . $idea->image_path) }}"
                                    alt="{{ $idea->title }}"
                                    class="h-w48uto w-full object-cover"
                                >
                            </div>
                        @endif

                        <h3 class="text-foreground text-lg">{{ $idea->title }}</h3>
                        <div class="mt-1">
                            <x-idea.status-label status="{{ $idea->status }}">
                                {{ $idea->status->label() }}
                            </x-idea.status-label>
                        </div>

                        <div class="mt-5 line-clamp-3">{{ $idea->description }}</div>
                        <div class="mt-4">{{ $idea->created_at->diffForHumans() }}</div>
                    </x-card>
                @empty
                    <x-card>
                        <p>No ideas at this time.</p>
                    </x-card>
                @endforelse
            </div>
        </div>

        <x-modal
            name="create-idea"
            title="New Idea"
        >
            <form
                x-data="{
                    status: 'pending',
                    newLink: '',
                    links: [],
                    newStep: '',
                    steps: []
                }"
                method="POST"
                action="{{ route('idea.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="space-y-6">
                    <x-form.field
                        label="Title"
                        name="title"
                        placeholder="Enter an idea for your title"
                        autofocus
                        required
                    />

                    <div class="space-y-2">
                        <label
                            for="status"
                            class="label"
                        >Status</label>

                        <div class="flex gap-x-3">
                            @foreach (App\IdeaStatus::cases() as $status)
                                <button
                                    type="button"
                                    @click="status = @js($status->value)"
                                    data-test="button-status-{{ $status->value }}"
                                    class="btn h-10 flex-1"
                                    :class="{ 'btn-outlined': status !== @js($status->value) }"
                                >
                                    {{ $status->label() }}
                                </button>
                            @endforeach

                            <input
                                type="hidden"
                                name="status"
                                :value="status"
                                class="input"
                            >
                        </div>

                        <x-form.error name="status" />
                    </div>

                    <x-form.field
                        label="Description"
                        name="description"
                        type="textarea"
                        placeholder="Describe your idea..."
                    />

                    <div class="space-y-2">
                        <label
                            for="iamge"
                            class="label"
                        >Feaured Image</label>

                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                        >
                        <x-form.error name="image" />
                    </div>

                    <div>
                        <fieldset class="space-y-2">
                            <legend class="label">Actionable Steps</legend>

                            <template
                                x-for="(step, index) in steps"
                                :key="step"
                            >
                                <div class="flex items-center gap-x-2">
                                    <input
                                        name="steps[]"
                                        x-model="step"
                                        class="input"
                                        readonly
                                    >

                                    <button
                                        type="button"
                                        aria-label="Remove step"
                                        @click="steps.splice(index, 1)"
                                        class="form-muted-icon"
                                    >
                                        <x-icons.close />
                                    </button>
                                </div>
                            </template>

                            <div class="flex items-center gap-x-2">
                                <input
                                    x-model="newStep"
                                    id="new-step"
                                    data-test="new-step"
                                    placeholder="What needs to be done?"
                                    class="input flex-1"
                                    spellcheck="false"
                                >

                                <button
                                    type="button"
                                    @click="steps.push(newStep.trim()); newStep = '';"
                                    data-test="submit-new-step-button"
                                    :disabled="newStep.trim().length === 0"
                                    aria-label="Add a new step"
                                    class="form-muted-icon"
                                >
                                    <x-icons.close class="rotate-45" />
                                </button>
                            </div>
                        </fieldset>
                    </div>

                    <div>
                        <fieldset class="space-y-2">
                            <legend class="label">Links</legend>

                            <template
                                x-for="(link, index) in links"
                                :key="link"
                            >
                                <div class="flex items-center gap-x-2">
                                    <input
                                        name="links[]"
                                        x-model="link"
                                        class="input"
                                        readonly
                                    >

                                    <button
                                        type="button"
                                        aria-label="Remove link"
                                        @click="links.splice(index, 1)"
                                        class="form-muted-icon"
                                    >
                                        <x-icons.close />
                                    </button>
                                </div>
                            </template>

                            <div class="flex items-center gap-x-2">
                                <input
                                    x-model="newLink"
                                    type="url"
                                    id="new-link"
                                    data-test="new-link"
                                    placeholder="http://example.com"
                                    autocomplete="url"
                                    class="input flex-1"
                                    spellcheck="false"
                                >

                                <button
                                    type="button"
                                    @click="links.push(newLink.trim()); newLink = '';"
                                    data-test="submit-new-link-button"
                                    :disabled="newLink.trim().length === 0"
                                    aria-label="Add a new link"
                                    class="form-muted-icon"
                                >
                                    <x-icons.close class="rotate-45" />
                                </button>
                            </div>
                        </fieldset>
                    </div>

                    <div class="flex justify-end gap-x-5">
                        <button
                            type="button"
                            @click="$dispatch('close-modal')"
                        >Cancel</button>
                        <button
                            type="submit"
                            class="btn"
                        >Create</button>
                    </div>
                </div>
            </form>
        </x-modal>
    </div>
</x-layout>
