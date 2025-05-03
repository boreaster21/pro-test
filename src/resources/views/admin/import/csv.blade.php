<x-app-layout>
    <x-slot name="header">
        <h2 class="p-admin-import__title">
            店舗情報 CSVインポート
        </h2>
    </x-slot>

    <div class="p-admin-import__container">
        <div class="p-admin-import__card">
            <div class="p-admin-import__content">
                @if (session('success'))
                    <div class="c-alert c-alert--success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="c-alert c-alert--error">
                        <p class="c-alert__title"><strong>エラーが発生しました:</strong></p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.import.csv') }}" method="POST" enctype="multipart/form-data" class="p-admin-import__form">
                    @csrf
                    <div class="p-admin-import__file-input-group">
                        <label for="csv_file" class="c-label">CSVファイルを選択</label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv"
                               class="p-admin-import__file-input" required>
                        <p class="p-admin-import__help-text">ヘッダー: 店舗名,地域,ジャンル,店舗概要,画像URL (UTF-8)</p>
                    </div>

                    <div class="p-admin-import__submit-wrapper">
                        <button type="submit"
                                class="c-button c-button--indigo">
                            インポート実行
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>