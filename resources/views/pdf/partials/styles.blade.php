<style>
    @page { margin: 14mm 16mm; size: A4; }

    * { box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 11px;
        line-height: 1.45;
        color: #1f2937;
        margin: 0;
        padding: 0;
    }

    table { border-collapse: collapse; }

    .page { width: 100%; }

    .top-band {
        background: #1e3a5f;
        color: #ffffff;
        padding: 14px 18px;
        margin-bottom: 22px;
    }

    .top-band td { vertical-align: middle; }

    .logo-box {
        width: 92px;
        height: 92px;
        background: #ffffff;
        border-radius: 8px;
        text-align: center;
        vertical-align: middle;
    }

    .logo-box img {
        max-width: 80px;
        max-height: 80px;
    }

    .logo-placeholder {
        width: 92px;
        height: 92px;
        background: rgba(255,255,255,0.12);
        border: 1px dashed rgba(255,255,255,0.45);
        border-radius: 8px;
        text-align: center;
        color: rgba(255,255,255,0.75);
        font-size: 9px;
        line-height: 92px;
    }

    .company-name {
        font-size: 20px;
        font-weight: bold;
        margin: 0 0 4px 0;
        letter-spacing: 0.3px;
    }

    .company-line {
        font-size: 10px;
        color: #dbeafe;
        margin: 1px 0;
    }

    .doc-badge { text-align: right; }

    .doc-badge .title {
        font-size: 22px;
        font-weight: bold;
        letter-spacing: 1.5px;
        margin: 0;
    }

    .doc-badge .ref {
        font-size: 10px;
        font-weight: bold;
        margin-top: 6px;
        color: #bfdbfe;
    }

    .info-row { width: 100%; margin-bottom: 18px; }

    .info-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 14px;
        background: #f9fafb;
        vertical-align: top;
    }

    .info-box h4 {
        margin: 0 0 8px 0;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 6px;
    }

    .info-box .name {
        font-size: 13px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 4px;
    }

    .address-line {
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px dashed #d1d5db;
        font-size: 10px;
        color: #374151;
        min-height: 28px;
    }

    .info-line {
        font-size: 10px;
        color: #374151;
        margin: 2px 0;
    }

    .info-line .label {
        color: #6b7280;
        display: inline-block;
        width: 72px;
    }

    .stats-row { width: 100%; margin-bottom: 18px; }

    .stat-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 10px;
        background: #f9fafb;
        text-align: center;
        vertical-align: top;
    }

    .stat-value {
        font-size: 16px;
        font-weight: bold;
        color: #1e3a5f;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6b7280;
    }

    .section-title {
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #1e3a5f;
        border-bottom: 2px solid #1e3a5f;
        padding-bottom: 6px;
        margin: 22px 0 12px 0;
    }

    .items-table {
        width: 100%;
        margin-bottom: 16px;
    }

    .items-table thead th {
        background: #1e3a5f;
        color: #ffffff;
        padding: 9px 10px;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        text-align: left;
    }

    .items-table thead th.right,
    .items-table tbody td.right { text-align: right; }

    .items-table thead th.center,
    .items-table tbody td.center { text-align: center; }

    .items-table tbody td {
        padding: 9px 10px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 10px;
        vertical-align: top;
    }

    .items-table tbody tr:nth-child(even) td {
        background: #f8fafc;
    }

    .stock-normal { color: #16a34a; font-weight: bold; }
    .stock-faible { color: #d97706; font-weight: bold; }
    .stock-rupture { color: #dc2626; font-weight: bold; }

    .info-box .name {
        font-size: 13px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 4px;
    }

    .address-line {
        margin-top: 6px;
        padding-top: 6px;
        border-top: 1px dashed #d1d5db;
        font-size: 10px;
        color: #374151;
        min-height: 28px;
    }

    .payment-box {
        border: 1px solid #dbeafe;
        background: #eff6ff;
        border-radius: 8px;
        padding: 12px 14px;
        vertical-align: top;
    }

    .payment-box h4 {
        margin: 0 0 8px 0;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #1d4ed8;
    }

    .bottom-row { width: 100%; }

    .totals-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        vertical-align: top;
    }

    .totals-box table { width: 100%; }

    .totals-box td {
        padding: 8px 12px;
        font-size: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .totals-box td.label { color: #6b7280; }

    .totals-box td.value {
        text-align: right;
        font-weight: bold;
        width: 120px;
    }

    .totals-box tr.grand td {
        background: #1e3a5f;
        color: #ffffff;
        font-size: 12px;
        font-weight: bold;
        border-bottom: none;
    }

    .notes-box {
        margin-top: 16px;
        padding: 10px 12px;
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        border-radius: 4px;
        font-size: 10px;
    }

    .signature-row { margin-top: 36px; width: 100%; }

    .signature-box {
        text-align: center;
        padding-top: 40px;
        border-top: 1px solid #9ca3af;
        font-size: 10px;
        color: #6b7280;
        width: 220px;
    }

    .summary-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .summary-box table { width: 100%; }

    .summary-box td {
        padding: 8px 12px;
        font-size: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .summary-box td.label { color: #6b7280; }

    .summary-box td.value {
        text-align: right;
        font-weight: bold;
    }

    .summary-box tr.grand td {
        background: #1e3a5f;
        color: #ffffff;
        font-weight: bold;
        border-bottom: none;
    }

    .footer {
        margin-top: 28px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        font-size: 9px;
        color: #9ca3af;
    }

    .empty-row td {
        text-align: center;
        color: #9ca3af;
        padding: 16px;
        font-style: italic;
    }
</style>
