<nav class="border-border border-b px-6">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between">
        <div>
            <a href="/">
                {{-- <img
                    src="/images/logo.png"
                    width="100"
                    height="auto"
                    alt="Idea logo"
                > --}}
                <x-icons.logo class="h-8 w-auto" />
            </a>
        </div>

        <div class="flex items-center gap-x-5">
            @auth
                <form
                    method="POST"
                    action="/logout"
                >
                    @csrf

                    <button>Log Out</button>
                </form>
            @endauth
            @guest

                <a href="/login">Sign In</a>
                <a
                    href="/register"
                    class="btn"
                >Register</a>
            @endguest
        </div>
    </div>
</nav>
