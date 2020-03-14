@push('scripts')
    <script defer src="{{ asset('/js/components/caeru_import.js') }}"></script>
@endpush
<section id="caeru_import">
    <section class="search_result">
        <p class="button right"><a class="mm_size m_height btn_gray" href="">フォーマットダウンロード</a></p>
        <p class="button right_10 right"><a class="m_size m_height btn_gray" @click="openImport">インポート</a></p>
    </section>
    {{ csrf_field() }}
    <input type="hidden" id="uploadUrl" value="{{ $url }}">
    <transition name="fade">
        <div class="caeru_import_wrapper" v-cloak v-show="importDisplay">
            <div class="caeru_import_inner">
                <h2>従業員情報インポート</h2>
                <div class="upload_box" id="uploadZone">
                    <section class="btn">
                        <p class="button"><a class="m_size m_height btn_blue">ファイル選択</a></p>
                        <p>一つ「.csv」ファイルを選択するか、枠内にファイルをドラッグ＆ドロップしてください</p>
                    </section>
                </div>
                <section class="btn">
                    <p class="button"><a class="modal-close mm_size l_height btn_gray l_font right_30" @click="startImport">インポート</a></p>
                    <p class="button"><a class="modal-close mm_size l_height btn_gray l_font" @click="closeImport">キャンセル</a></p>
                </section>
            </div>
            <div class="modal-overlay" @click="closeImport"></div>
        </div>
    </transition>
</section>