@once
    @php
        $garageErrorBags = [];
        foreach ($errors->getBags() as $garageBagName => $garageBag) {
            $garageErrorBags[$garageBagName] = $garageBag->getMessages();
        }
    @endphp
    <link rel="stylesheet" href="{{ asset('css/form-ui.css') }}">
    <script type="application/json" id="garage-form-errors">@json($garageErrorBags)</script>

    <div class="modal fade" id="garageConfirmModal" tabindex="-1"
         aria-labelledby="garageConfirmTitle" aria-describedby="garageConfirmMessage" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="garageConfirmTitle">Xác nhận thao tác</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body" id="garageConfirmMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-confirm-cancel>Quay lại</button>
                    <button type="button" class="btn btn-danger" data-confirm-accept>Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/form-ui.js') }}" defer></script>
@endonce
