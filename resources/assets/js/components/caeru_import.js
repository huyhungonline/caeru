import Dropzone from '../components/dropzone.js';

var caeru_import = new Vue({
    el: "#caeru_import",
    data: {
        importDisplay: false,
        instance: null,
    },
    methods: {
        openImport: function() {
            this.importDisplay = true;
        },
        closeImport: function() {
            this.importDisplay = false;
            this.instance.removeAllFiles();
        },
        startImport: function() {
            this.instance.processQueue();
        },
    },
    mounted: function() {
        this.instance = new Dropzone("div#uploadZone", {
            url: $('input#uploadUrl').val(),
            autoProcessQueue: false,
            paramName: 'data',
            headers: { 'X-CSRF-TOKEN' : $('input[name="_token"]').val() },
            previewTemplate: `
                <div class="dz-preview dz-file-preview">
                    <div class="dz-details">
                        <div class="dz-filename"><span data-dz-name></span><span data-dz-remove><i class="fa fa-times-circle" aria-hidden="true"></i></span></div>
                    </div>
                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                </div>`,
            autoProcessQueue: false,
            createImageThumbnails: false,
            clickable: "#uploadZone .btn .button a.btn_blue",
            maxFiles: 1,
            uploadMultiple: false,
            acceptedFiles: '.csv',
            maxFilesize: 3, // In MB
            dictFileTooBig: 'ファイルのサイズが大きすぎます（{{filesize}} MB）。最大のサイズは：{{maxFilesize}} MBです。',
            init: function() {
                this.on('maxfilesexceeded', (file) => {
                    this.removeFile(file);
                });
                this.on('addedfile', (file) => {
                    if (file.type !== 'text/csv') {
                        this.removeFile(file);
                        return;
                    }
                })

                // On success
                this.on('success', (file, response) => {
                    alert(response['message']);
                })
            },
        })
    },
})