@extends('layouts.admin')

@section('title', 'Kelola Customer - Admin UMKMART')
@section('page_title', 'Daftar Customer Terdaftar')

@section('content')
<div class="bg-white border border-slate-150 rounded-2xl p-6 shadow-sm text-left">
    @if($customers->count() > 0)
        <div class="card-body" style="max-width: 100%; padding: 0;">
            <div style="display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table" style="width: 100%; min-width: max-content; border-collapse: collapse; text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold uppercase border-b border-slate-150">
                            <th style="white-space: nowrap !important; padding: 12px 20px !important;">NAMA CUSTOMER</th>
                            <th style="white-space: nowrap !important; padding: 12px 20px !important;">ALAMAT EMAIL</th>
                            <th style="white-space: nowrap !important; padding: 12px 20px !important;">NOMOR TELEPON</th>
                            <th style="white-space: nowrap !important; padding: 12px 20px !important;">TOTAL PESANAN</th>
                            <th style="white-space: nowrap !important; padding: 12px 20px !important;">TANGGAL BERGABUNG</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($customers as $c)
                            <tr class="hover:bg-slate-50/50">
                                <td style="white-space: nowrap !important; padding: 12px 20px !important;" class="font-bold text-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-emerald-600 font-bold flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($c->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $c->name }}</span>
                                    </div>
                                </td>
                                <td style="white-space: nowrap !important; padding: 12px 20px !important;" class="text-slate-600 font-semibold break-all">{{ $c->email }}</td>
                                <td style="white-space: nowrap !important; padding: 12px 20px !important;" class="text-slate-500 font-semibold">{{ $c->phone_number }}</td>
                                <td style="white-space: nowrap !important; padding: 12px 20px !important;" class="font-bold text-slate-800">{{ $c->orders_count }} Transaksi</td>
                                <td style="white-space: nowrap !important; padding: 12px 20px !important;" class="text-slate-400">{{ $c->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $customers->links() }}
        </div>
    @else
        <div class="py-8 text-center text-slate-400">
            <span class="material-icons text-4xl">people_outline</span>
            <p class="text-xs font-bold mt-2">Tidak ada customer terdaftar.</p>
        </div>
    @endif
</div>
@endsection
