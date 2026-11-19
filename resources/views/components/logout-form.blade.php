@props(['buttonClass' => ''])

<form method="POST" action="{{ route('logout') }}" onsubmit="this.elements.auth_token.value = sessionStorage.getItem('auth_token') || ''; sessionStorage.removeItem('auth_token'); sessionStorage.removeItem('user');">
    @csrf
    <input type="hidden" name="auth_token" value="">
    <button type="submit" class="{{ $buttonClass }}">Keluar</button>
</form>
