@extends('layouts.admin')
@section('title', 'Import dữ liệu')
@section('page-title', 'Import dữ liệu')

@section('content')
<div class="space-y-6" x-data="universalImport()">

    {{-- Upload card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-start justify-between flex-wrap gap-4 mb-4">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Nhập dữ liệu từ file Excel</h2>
                <p class="text-sm text-gray-500 mt-1">
                    File .xlsx/.xls, tối đa {{ number_format($maxFileSizeKb / 1024, 1) }}MB. Sheet được hỗ trợ:
                    <span class="font-mono text-xs text-gray-600">{{ implode(', ', $sheetKeys) }}</span>.
                    File trên {{ number_format($queueThreshold) }} dòng sẽ tự động xử lý ở chế độ nền.
                </p>
            </div>
            <a href="{{ route('admin.import.template') }}"
               class="shrink-0 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Tải file mẫu
            </a>
        </div>

        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center"
             :class="{ 'border-blue-400 bg-blue-50': dragging }"
             x-on:dragover.prevent="dragging = true"
             x-on:dragleave.prevent="dragging = false"
             x-on:drop.prevent="dragging = false; onFileSelected($event.dataTransfer.files[0])">
            <input type="file" class="hidden" x-ref="fileInput" accept=".xlsx,.xls"
                   x-on:change="onFileSelected($event.target.files[0])">
            <template x-if="!fileName">
                <div>
                    <p class="text-sm text-gray-500 mb-3">Kéo thả file vào đây hoặc</p>
                    <button type="button" x-on:click="$refs.fileInput.click()"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Chọn file Excel
                    </button>
                </div>
            </template>
            <template x-if="fileName">
                <div class="flex items-center justify-center gap-3">
                    <span class="text-sm font-medium text-gray-800" x-text="fileName"></span>
                    <span class="text-xs text-gray-400" x-text="fileSizeLabel"></span>
                    <button type="button" x-on:click="reset()" class="text-xs text-red-600 hover:text-red-800">Bỏ chọn</button>
                </div>
            </template>
        </div>

        <p class="text-sm mt-3" :class="error ? 'text-red-600' : 'text-transparent select-none'" x-text="error || '.'"></p>

        <div class="flex flex-wrap gap-3 mt-4">
            <button type="button" x-on:click="submit(true)" :disabled="!fileName || busy"
                    class="px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                Kiểm tra dữ liệu (Dry Run)
            </button>
            <button type="button" x-on:click="submit(false)" :disabled="!fileName || busy"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                Import
            </button>
        </div>
    </div>

    {{-- Progress --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-show="busy" x-cloak>
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700" x-text="statusLabel"></span>
            <span class="text-sm text-gray-500" x-text="percent + '%'"></span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + percent + '%'"></div>
        </div>
    </div>

    {{-- Report --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-show="report" x-cloak>
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <div>
                <h3 class="text-base font-semibold text-gray-800">
                    <span x-show="report && report.dry_run">Kết quả kiểm tra dữ liệu</span>
                    <span x-show="report && !report.dry_run">Kết quả import</span>
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    <span x-text="report ? report.total_success : 0"></span> thành công,
                    <span class="text-red-600" x-text="report ? report.total_fail : 0"></span> lỗi —
                    <span x-text="report ? (report.duration_ms / 1000).toFixed(1) : 0"></span>s
                </p>
            </div>
            <a x-show="report && report.has_errors" :href="errorsUrl()"
               class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                Tải file lỗi (import_error.xlsx)
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Sheet</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Số dòng</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Thành công</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Lỗi</th>
                        <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(sheet, key) in (report ? report.sheets : {})" :key="key">
                        <tr>
                            <td class="px-4 py-2 font-mono text-xs text-gray-700" x-text="key"></td>
                            <td class="px-4 py-2 text-gray-600" x-text="sheet.total"></td>
                            <td class="px-4 py-2 text-emerald-600 font-medium" x-text="sheet.success"></td>
                            <td class="px-4 py-2" :class="sheet.fail > 0 ? 'text-red-600 font-medium' : 'text-gray-400'" x-text="sheet.fail"></td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                <template x-if="sheet.header_error">
                                    <span class="text-red-600" x-text="sheet.header_error"></span>
                                </template>
                                <template x-for="err in (sheet.errors || []).slice(0, 5)" :key="err.row">
                                    <div>Dòng <span x-text="err.row"></span>: <span x-text="err.message"></span></div>
                                </template>
                                <template x-if="(sheet.errors || []).length > 5">
                                    <div class="text-gray-400">... và <span x-text="sheet.errors.length - 5"></span> lỗi khác (xem file lỗi)</div>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Nhật ký Import --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Nhật ký Import</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thời gian</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Admin</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">File</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thành công</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Lỗi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thời gian xử lý</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $log->thoi_gian?->format('d/m/Y H:i:s') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $log->nguoiThucHien?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $log->payload['file_name'] ?? '—' }}</td>
                        <td class="px-5 py-3 text-emerald-600 font-medium">{{ $log->payload['total_success'] ?? 0 }}</td>
                        <td class="px-5 py-3 {{ ($log->payload['total_fail'] ?? 0) > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                            {{ $log->payload['total_fail'] ?? 0 }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ isset($log->payload['duration_ms']) ? number_format($log->payload['duration_ms'] / 1000, 1) . 's' : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có lượt import nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $logs->links() }}</div>
        @endif
    </div>
</div>

<script>
function universalImport() {
    return {
        fileName: null,
        fileSizeLabel: '',
        dragging: false,
        busy: false,
        percent: 0,
        statusLabel: '',
        report: null,
        error: null,
        token: null,

        onFileSelected(file) {
            if (!file) return;
            this.$refs.fileInput.files = this.buildFileList(file);
            this.fileName = file.name;
            this.fileSizeLabel = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            this.report = null;
            this.error = null;
        },

        buildFileList(file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            return dt.files;
        },

        reset() {
            this.fileName = null;
            this.$refs.fileInput.value = '';
            this.report = null;
            this.error = null;
        },

        errorsUrl() {
            return this.token ? '{{ url('admin/import/errors') }}/' + this.token : '#';
        },

        async submit(dryRun) {
            if (!this.fileName || this.busy) return;
            this.busy = true;
            this.error = null;
            this.report = null;
            this.percent = 0;
            this.statusLabel = dryRun ? 'Đang kiểm tra dữ liệu...' : 'Đang import...';

            const formData = new FormData();
            formData.append('file', this.$refs.fileInput.files[0]);
            formData.append('dry_run', dryRun ? '1' : '0');

            try {
                const res = await fetch('{{ route('admin.import.run') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });
                const data = await res.json();

                if (!res.ok) {
                    this.error = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Có lỗi xảy ra.');
                    this.busy = false;
                    return;
                }

                this.token = data.token || null;

                if (data.queued) {
                    this.statusLabel = 'Đang xử lý ở chế độ nền...';
                    await this.poll();
                } else {
                    this.token = data.token;
                    this.applyReport(data);
                    this.busy = false;
                }
            } catch (e) {
                this.error = 'Không thể kết nối máy chủ.';
                this.busy = false;
            }
        },

        async poll() {
            const url = '{{ url('admin/import/status') }}/' + this.token;
            const tick = async () => {
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.percent = data.percent ?? this.percent;

                    if (data.status && data.status !== 'running' && data.status !== 'queued') {
                        this.applyReport(data);
                        this.busy = false;
                        return;
                    }

                    setTimeout(tick, 1500);
                } catch (e) {
                    this.error = 'Mất kết nối khi theo dõi tiến trình.';
                    this.busy = false;
                }
            };
            await tick();
        },

        applyReport(data) {
            this.percent = 100;
            this.report = data;
        },
    };
}
</script>
@endsection
