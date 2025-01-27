<nav id="component">
    @if ($showFooLink)
        <a href="#" class="nav-link" id="foo">Dashboard</a>
    @endif

    @if ($showBarLink)
        <a href="#" class="nav-link" id="bar">Settings</a>
    @endif

    @if ($showBarLink)
        <a href="#" class="nav-link" id="baz">Logout</a>
    @endif
</nav>
