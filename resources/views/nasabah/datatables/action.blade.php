<a href="{{ route('nasabah.show', $nasabah->id) }}" class="btn btn-secondary btn-sm">Lihat</a>
<a href="{{ route('nasabah.edit', $nasabah->id) }}" class="btn btn-primary btn-sm">Edit</a>
<form class="d-inline" action="{{ route('nasabah.destroy', $nasabah->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data nasabah ini?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
</form>
