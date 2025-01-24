<div id="component">
    <ul>
        @foreach (['foo', 'bar', 'baz', 'qux'] as $id)
            <li @class(['bullet-point']) id="{{ $id }}">
                {{ ucfirst($id) }}
            </li>
        @endforeach
    </ul>
</div>
