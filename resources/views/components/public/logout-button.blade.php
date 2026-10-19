@props(['class' => 'dropdown-item text-danger'])
<form novalidate method="POST" action="{{ route('logout') }}" data-confirm="Bạn muốn đăng xuất khỏi tài khoản?" data-confirm-title="Đăng xuất" data-confirm-accept="Đăng xuất" data-confirm-cancel="Ở lại">
    @csrf
    <button type="submit" class="{{ $class }}"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Đăng xuất</button>
</form>
