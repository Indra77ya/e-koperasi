<a class="btn btn-sm btn-secondary" href="{{ route('nasabahs.show', $nasabah->id) }}">Detail</a>
<a class="btn btn-sm btn-primary" href="{{ route('nasabahs.edit', $nasabah->id) }}">Edit</a>
<form action="{{ route('nasabahs.destroy', $nasabah->id) }}" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Hapus</button>
</form>
