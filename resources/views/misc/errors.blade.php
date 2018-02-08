@if ($errors->any() || session('error'))
    <div class="alert alert-danger" style="margin-left: 1.5rem; margin-right: 1.5rem">
        <ul>
            @if (session('error'))
                <span>{{ session('error') }}</span>
            @endif
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif