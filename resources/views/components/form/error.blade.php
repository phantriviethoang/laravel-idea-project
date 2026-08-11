@props(['name'])

@error('status')
<p class="error">{{ $message }}</p>
@enderror
