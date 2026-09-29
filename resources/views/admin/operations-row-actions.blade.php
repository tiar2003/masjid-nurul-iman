<div class="operations-actions">
    <a href="{{ route('operations.index', ['module' => $module, 'year' => $year, 'edit' => $item->id]) }}">Ubah</a>
    <form action="{{ route('operations.destroy', ['module' => $module, 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')">
        @csrf
        @method('DELETE')
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit">Hapus</button>
    </form>
</div>