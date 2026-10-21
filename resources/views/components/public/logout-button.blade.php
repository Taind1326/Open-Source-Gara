@props(['class' => 'dropdown-item text-danger'])
<form method="POST" action="{{ route('logout') }}" onsubmit="return window.confirm('Bạn muốn đăng xuất khỏi tài khoản?');">
    @csrf
    <button type="submit" class="{{ $class }}"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Đăng xuất</button>
</form>
