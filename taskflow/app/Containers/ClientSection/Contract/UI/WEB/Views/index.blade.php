<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hợp đồng KOC | Taskflow</title>
    <meta name="description" content="Hệ thống quản lý và tạo hợp đồng KOC tự động từ dữ liệu Excel">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: transparent;
            --bg-secondary: rgba(0, 0, 0, 0.2);
            --bg-card: rgba(0, 0, 0, 0.35);
            --bg-card-hover: rgba(0, 0, 0, 0.45);
            --bg-input: rgba(0, 0, 0, 0.4);
            --border-color: rgba(255, 255, 255, 0.1);
            --border-hover: rgba(255, 255, 255, 0.2);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.5);
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --accent-glow: rgba(99, 102, 241, 0.4);
            --success: #34d399;
            --success-bg: rgba(16, 185, 129, 0.2);
            --warning: #fbbf24;
            --warning-bg: rgba(245, 158, 11, 0.2);
            --danger: #f87171;
            --cyan: #22d3ee;
            --cyan-bg: rgba(6, 182, 212, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: transparent;
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Background Image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/images/bg.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: -2;
        }



        .container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .page-header__title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-header__icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 8px 24px var(--accent-glow);
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-header__subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 0.125rem;
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }

        .stat-card--total::before { background: linear-gradient(90deg, var(--accent), var(--cyan)); }
        .stat-card--pending::before { background: linear-gradient(90deg, var(--warning), #f97316); }
        .stat-card--done::before { background: linear-gradient(90deg, var(--success), #34d399); }

        .stat-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .stat-card__label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-card__value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card--total .stat-card__value { color: var(--accent-hover); }
        .stat-card--pending .stat-card__value { color: var(--warning); }
        .stat-card--done .stat-card__value { color: var(--success); }

        /* Controls container */
        .controls-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        /* Search bar */
        .search-bar {
            display: flex;
            gap: 0.75rem;
            flex: 1;
            min-width: 300px;
        }

        .search-bar__input-wrapper {
            flex: 1;
            position: relative;
        }

        .search-bar__icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }

        .search-bar__input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            outline: none;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .search-bar__input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .search-bar__input::placeholder {
            color: var(--text-muted);
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.8), rgba(79, 70, 229, 0.8));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            white-space: nowrap;
        }

        .btn-action:hover {
            background: linear-gradient(135deg, rgba(129, 140, 248, 0.9), rgba(99, 102, 241, 0.9));
            box-shadow: 0 4px 16px var(--accent-glow);
            transform: translateY(-1px);
        }

        .upload-form {
            display: flex;
            gap: 0.5rem;
        }

        .upload-form input[type="file"] {
            display: none;
        }

        .upload-form label {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
        }
        
        .upload-form label:hover {
            background: rgba(16, 185, 129, 0.3);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.2);
        }

        /* Table */
        .table-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            background: rgba(99, 102, 241, 0.06);
            border-bottom: 1px solid var(--border-color);
        }

        th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        td {
            padding: 1rem 1.25rem;
            font-size: 0.875rem;
            border-bottom: 1px solid rgba(42, 53, 80, 0.5);
            vertical-align: middle;
        }

        tr {
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: var(--bg-card-hover);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Cell styles */
        .cell-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .cell-name__sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.125rem;
        }

        .cell-username {
            color: var(--cyan);
            font-weight: 500;
        }

        .cell-username a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }

        .cell-username a:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }

        .cell-amount {
            font-weight: 700;
            color: var(--success);
            font-variant-numeric: tabular-nums;
        }

        .cell-product {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
        }

        .tag {
            display: inline-block;
            padding: 0.25rem 0.625rem;
            background: var(--cyan-bg);
            color: var(--cyan);
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .tag--category {
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent-hover);
            border-color: rgba(99, 102, 241, 0.2);
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.3125rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge--pending {
            background: var(--warning-bg);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge--done {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge__dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Button */
        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--accent), #4f46e5);
            border: none;
            border-radius: 10px;
            color: white;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-generate:hover {
            background: linear-gradient(135deg, var(--accent-hover), var(--accent));
            box-shadow: 0 4px 16px var(--accent-glow);
            transform: translateY(-1px);
        }

        .btn-generate:active {
            transform: translateY(0);
        }

        .btn-generate--done {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-generate--done:hover {
            background: var(--bg-card-hover);
            box-shadow: none;
            border-color: var(--border-hover);
        }

        .btn-generate--loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .btn-generate__spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }

        .btn-generate--loading .btn-generate__spinner {
            display: block;
        }

        .btn-generate--loading .btn-generate__icon {
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-state__icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state__text {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        /* Row number */
        .cell-row-num {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
            font-variant-numeric: tabular-nums;
        }

        /* Date cell */
        .cell-date {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-variant-numeric: tabular-nums;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 1000;
            transform: translateY(120%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }

        .toast--show {
            transform: translateY(0);
        }

        .toast--success {
            background: var(--success);
            color: white;
        }

        .toast--error {
            background: var(--danger);
            color: white;
        }

        /* Info tooltip */
        .info-popover {
            position: relative;
            cursor: pointer;
        }

        .info-popover__trigger {
            color: var(--text-muted);
            font-size: 0.75rem;
            transition: color 0.2s;
        }

        .info-popover__trigger:hover {
            color: var(--accent-hover);
        }

        .info-popover__content {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.875rem 1rem;
            min-width: 280px;
            max-width: 360px;
            z-index: 100;
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            font-size: 0.75rem;
            color: var(--text-secondary);
            line-height: 1.5;
            white-space: pre-line;
            margin-bottom: 0.5rem;
        }

        .info-popover:hover .info-popover__content {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0.75rem;
            }

            .page-header__top {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .page-header h1 {
                font-size: 1.1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.75rem 1rem;
            }

            .stat-card__value {
                font-size: 1.5rem;
            }

            .controls-container {
                flex-direction: column;
                gap: 0.75rem;
            }

            .search-bar {
                min-width: unset;
                width: 100%;
            }

            .upload-form {
                width: 100%;
            }

            .upload-form label {
                width: 100%;
                justify-content: center;
            }

            /* ─── Card layout for table on mobile ─── */
            .table-scroll {
                overflow-x: unset;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            /* Hide desktop thead */
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tbody tr {
                background: var(--bg-card);
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid var(--border-color);
                border-radius: 14px;
                margin-bottom: 0.85rem;
                padding: 0.85rem 1rem;
            }

            tbody tr:hover {
                background: var(--bg-card-hover);
            }

            td {
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
                padding: 0.35rem 0;
                border: none;
                font-size: 0.83rem;
            }

            /* Label before each cell */
            td::before {
                content: attr(data-label);
                font-size: 0.68rem;
                font-weight: 600;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                min-width: 90px;
                flex-shrink: 0;
                padding-top: 0.15rem;
            }

            /* Hide row number on mobile */
            td.cell-row-num {
                display: none;
            }

            /* Actions cell: 2 buttons side by side */
            td:last-child > div {
                flex-direction: row;
                gap: 0.4rem;
                flex-wrap: wrap;
            }

            .btn-generate {
                flex: 1;
                min-width: 0;
                justify-content: center;
                padding: 0.5rem 0.4rem;
                font-size: 0.72rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .btn-generate__text {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div style="background: var(--success-bg); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1rem; border: 1px solid var(--success);">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: var(--danger); color: white; padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: var(--danger); color: white; padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="page-header">
            <div class="page-header__top">
                <div class="page-header__title">
                    <div class="page-header__icon">📄</div>
                    <div>
                        <h1>Quản lý Hợp đồng KOC</h1>
                        <div class="page-header__subtitle">Tạo hợp đồng tự động từ dữ liệu Excel</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card stat-card--total">
                <div class="stat-card__label">Tổng số hợp đồng</div>
                <div class="stat-card__value">{{ $contracts->count() }}</div>
            </div>
            <div class="stat-card stat-card--pending">
                <div class="stat-card__label">Chưa tạo HĐ</div>
                <div class="stat-card__value">{{ $contracts->where('is_generated', false)->count() }}</div>
            </div>
            <div class="stat-card stat-card--done">
                <div class="stat-card__label">Đã tạo HĐ</div>
                <div class="stat-card__value">{{ $contracts->where('is_generated', true)->count() }}</div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls-container">
            <form class="search-bar" method="GET" action="{{ route('contracts.index') }}">
                <div class="search-bar__input-wrapper">
                    <span class="search-bar__icon">🔍</span>
                    <input
                        type="text"
                        name="search"
                        class="search-bar__input"
                        placeholder="Tìm kiếm theo tên KOC, username, sản phẩm..."
                        value="{{ $search ?? '' }}"
                        id="search-input"
                    >
                </div>
                <button type="submit" class="btn-action">
                    <span>Tìm kiếm</span>
                </button>
            </form>

            <form action="{{ route('contracts.import') }}" method="POST" enctype="multipart/form-data" class="upload-form" id="upload-form">
                @csrf
                <input type="file" name="excel_file" id="excel-file" accept=".xlsx,.xls" onchange="document.getElementById('upload-form').submit()">
                <label for="excel-file" class="btn-action">
                    <span>📤 Nhập Excel</span>
                </label>
            </form>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <div class="table-scroll">
                @if($contracts->count() > 0)
                <table>
                    <colgroup>
                        <col style="width:3%">   {{-- # --}}
                        <col style="width:7%">   {{-- Ngày --}}
                        <col style="width:16%">  {{-- Tên KOC --}}
                        <col style="width:11%">  {{-- TikTok --}}
                        <col style="width:13%">  {{-- Sản phẩm --}}
                        <col style="width:15%">  {{-- Hạng mục --}}
                        <col style="width:10%">  {{-- Giá trị --}}
                        <col style="width:10%">  {{-- Link Video --}}
                        <col style="width:15%">  {{-- Thao tác --}}
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ngày</th>
                            <th>Tên KOC</th>
                            <th>TikTok</th>
                            <th>Sản phẩm</th>
                            <th class="col-hide-mobile">Hạng mục</th>
                            <th>Giá trị HĐ</th>
                            <th>Link Video</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $index => $contract)
                        <tr id="row-{{ $contract->id }}">
                            <td class="cell-row-num">{{ $index + 1 }}</td>
                            <td class="cell-date" data-label="Ngày">{{ $contract->contract_date ? $contract->contract_date->format('d/m/Y') : '-' }}</td>
                            <td data-label="Tên KOC">
                                <div class="cell-name">{{ $contract->full_name ?? $contract->koc_name ?? '-' }}</div>
                                <div class="cell-name__sub info-popover">
                                    <span class="info-popover__trigger">ℹ️ Xem thông tin</span>
                                    <div class="info-popover__content">{{ $contract->personal_info_raw }}</div>
                                </div>
                            </td>
                            <td class="cell-username" data-label="TikTok">
                                @if($contract->tiktok_url)
                                    <a href="{{ $contract->tiktok_url }}" target="_blank" rel="noopener">
                                        &#64;{{ $contract->tiktok_username }}
                                    </a>
                                @else
                                    &#64;{{ $contract->tiktok_username }}
                                @endif
                            </td>
                            <td data-label="Sản phẩm">
                                <div class="cell-product">
                                    @if($contract->product)
                                        <span class="tag">{{ $contract->product }}</span>
                                    @else
                                        <span style="color: var(--text-muted)">-</span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Hạng mục">
                                @if($contract->category)
                                    <span class="tag tag--category">{{ Str::limit($contract->category, 30) }}</span>
                                @else
                                    <span style="color: var(--text-muted)">-</span>
                                @endif
                            </td>
                            <td class="cell-amount" data-label="Giá trị HĐ">
                                {{ $contract->amount_raw ? number_format((int)str_replace([',', '.'], '', $contract->amount_raw)) : '-' }}
                            </td>
                            <td data-label="Link Video" style="text-align:left;">
                                @if($contract->tiktok_video_url)
                                    <a href="{{ $contract->tiktok_video_url }}"
                                       target="_blank"
                                       rel="noopener"
                                       title="{{ $contract->tiktok_video_url }}"
                                       style="display:inline-flex;align-items:center;gap:0.35rem;background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:0.35rem 0.65rem;color:var(--text-primary);font-size:0.75rem;text-decoration:none;transition:all 0.2s;white-space:nowrap;">
                                        🎵 Xem video
                                    </a>
                                @else
                                    <span style="color:var(--text-muted)">-</span>
                                @endif
                            </td>
                            <td data-label="Thao tác">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <a
                                        href="{{ route('contracts.generate', $contract->id) }}"
                                        class="btn-generate {{ $contract->is_generated ? 'btn-generate--done' : '' }}"
                                        onclick="handleGenerate(event, this, {{ $contract->id }})"
                                        id="btn-{{ $contract->id }}"
                                    >
                                        <span class="btn-generate__spinner"></span>
                                        <span class="btn-generate__icon">📥</span>
                                        <span class="btn-generate__text">{{ $contract->is_generated ? 'Tải lại HĐ' : 'Tạo HĐ' }}</span>
                                    </a>

                                    <a
                                        href="{{ route('contracts.generate_bbnt', $contract->id) }}"
                                        class="btn-generate {{ $contract->is_generated ? 'btn-generate--done' : '' }}"
                                        onclick="handleGenerateBbnt(event, this, {{ $contract->id }})"
                                        id="btn-bbnt-{{ $contract->id }}"
                                    >
                                        <span class="btn-generate__spinner"></span>
                                        <span class="btn-generate__icon">📑</span>
                                        <span class="btn-generate__text">Tải BBNT</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-state__icon">📭</div>
                    <div class="empty-state__text">Không tìm thấy hợp đồng nào</div>
                    <div style="color: var(--text-muted); font-size: 0.8rem;">
                        @if($search)
                            Thử tìm kiếm với từ khóa khác
                        @else
                            Hãy chạy <code style="background: var(--bg-input); padding: 0.25rem 0.5rem; border-radius: 4px;">php artisan contracts:import</code> để import dữ liệu
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
        function handleGenerate(event, btn, contractId) {
            // Add loading state
            btn.classList.add('btn-generate--loading');

            // After download completes (estimated 3s), update UI
            setTimeout(function() {
                btn.classList.remove('btn-generate--loading');
                btn.classList.add('btn-generate--done');
                btn.querySelector('.btn-generate__text').textContent = 'Tải lại';

                // Update badge
                const row = document.getElementById('row-' + contractId);
                if (row) {
                    const badge = row.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge badge--done';
                        badge.innerHTML = '<span class="badge__dot"></span> Đã tạo';
                    }
                }

                showToast('✅ Tạo hợp đồng thành công!', 'success');

                // Update stats
                updateStats();
            }, 2000);
        }

        function handleGenerateBbnt(event, btn, contractId) {
            // Add loading state
            btn.classList.add('btn-generate--loading');

            // After download completes, update UI
            setTimeout(function() {
                btn.classList.remove('btn-generate--loading');
                btn.classList.add('btn-generate--done');
                showToast('✅ Tạo BBNT thành công!', 'success');
            }, 2000);
        }

        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast toast--' + type + ' toast--show';

            setTimeout(function() {
                toast.classList.remove('toast--show');
            }, 3000);
        }

        function updateStats() {
            const doneCount = document.querySelectorAll('.badge--done').length;
            const totalCount = document.querySelectorAll('tbody tr').length;
            const pendingCount = totalCount - doneCount;

            const statValues = document.querySelectorAll('.stat-card__value');
            if (statValues.length >= 3) {
                statValues[1].textContent = pendingCount;
                statValues[2].textContent = doneCount;
            }
        }

        // Client-side live search
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') return; // Let form submit handle Enter

                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr');

                if (query.length === 0) {
                    rows.forEach(function(row) { row.style.display = ''; });
                    return;
                }

                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>
