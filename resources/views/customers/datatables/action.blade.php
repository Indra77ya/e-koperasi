<form action="{{ route('customers.destroy', $customer->id) }}" method="post">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
    <a class="btn btn-sm btn-info" href="{{ route('customers.show', $customer->id) }}">Detail</a>
    <a class="btn btn-sm btn-primary" href="{{ route('customers.edit', $customer->id) }}">Edit</a>
    <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
</form>
