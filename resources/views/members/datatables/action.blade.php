<a class="btn btn-sm btn-info" href="{{ route('members.show', $member->id) }}">{{ __('detail') }}</a>
<a class="btn btn-sm btn-primary" href="{{ route('members.edit', $member->id) }}">{{ __('edit') }}</a>
<form action="{{ route('members.destroy', $member->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('action_confirm_destroy') }}')">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
    <button class="btn btn-sm btn-danger" type="submit">{{ __('destroy') }}</button>
</form>
