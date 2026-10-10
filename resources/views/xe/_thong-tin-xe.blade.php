{{-- Phần chung của form xe (khách + admin): Hãng → Năm → Dòng, gợi ý qua API, vẫn cho tự gõ --}}
<div class="grid2">
    <div class="field">
        <label for="HangXe">Hãng xe</label>
        <input type="text" id="HangXe" name="HangXe" value="{{ old('HangXe', $xe->HangXe) }}" maxlength="50" required
               list="ds-hang" autocomplete="off" class="@error('HangXe') is-invalid @enderror">
        <datalist id="ds-hang"></datalist>
        @error('HangXe')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="NamSanXuat">Năm sản xuất <span class="opt">(không bắt buộc)</span></label>
        <input type="text" inputmode="numeric" id="NamSanXuat" name="NamSanXuat" value="{{ old('NamSanXuat', $xe->NamSanXuat) }}"
               maxlength="4" list="ds-nam" autocomplete="off" class="@error('NamSanXuat') is-invalid @enderror">
        <datalist id="ds-nam"></datalist>
        @error('NamSanXuat')<div class="err">{{ $message }}</div>@enderror
    </div>
</div>

<div class="grid2">
    <div class="field">
        <label for="DongXe">Dòng xe <span class="opt">(không bắt buộc)</span></label>
        <input type="text" id="DongXe" name="DongXe" value="{{ old('DongXe', $xe->DongXe) }}" maxlength="50"
               list="ds-dong" autocomplete="off" class="@error('DongXe') is-invalid @enderror">
        <datalist id="ds-dong"></datalist>
        <div class="hint">Chọn Hãng → Năm để gợi ý dòng xe, hoặc tự nhập nếu không có trong danh sách.</div>
        @error('DongXe')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="MauSac">Màu sắc <span class="opt">(không bắt buộc)</span></label>
        <input type="text" id="MauSac" name="MauSac" value="{{ old('MauSac', $xe->MauSac) }}" maxlength="30"
               class="@error('MauSac') is-invalid @enderror">
        @error('MauSac')<div class="err">{{ $message }}</div>@enderror
    </div>
</div>

<script>
(function () {
    var url = {
        hang: @json(route('xe-api.hang')),
        nam:  @json(route('xe-api.nam')),
        dong: @json(route('xe-api.dong'))
    };
    var hang = document.getElementById('HangXe'),
        nam  = document.getElementById('NamSanXuat'),
        dong = document.getElementById('DongXe');

    function napDanhSach(id, giaTri) {
        var dl = document.getElementById(id);
        dl.innerHTML = '';
        giaTri.forEach(function (v) {
            var o = document.createElement('option');
            o.value = v;
            dl.appendChild(o);
        });
    }

    function lay(link, thamSo, id) {
        var q = new URLSearchParams(thamSo).toString();
        return fetch(link + (q ? '?' + q : ''), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : []; })
            .then(function (ds) { napDanhSach(id, ds); })
            .catch(function () { napDanhSach(id, []); });
    }

    function napNam()  { return hang.value.trim() ? lay(url.nam, { hang: hang.value.trim() }, 'ds-nam') : napDanhSach('ds-nam', []); }
    function napDong() {
        var h = hang.value.trim(), n = nam.value.trim();
        return (h && /^\d{4}$/.test(n)) ? lay(url.dong, { hang: h, nam: n }, 'ds-dong') : napDanhSach('ds-dong', []);
    }

    lay(url.hang, {}, 'ds-hang');
    napNam(); napDong();
    hang.addEventListener('change', function () { napNam(); napDong(); });
    hang.addEventListener('input', function () { napNam(); napDong(); });
    nam.addEventListener('input', napDong);
    nam.addEventListener('change', napDong);
})();
</script>
