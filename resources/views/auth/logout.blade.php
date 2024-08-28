{{-- force logout  form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<script>
    document.getElementById('logout-form').submit();
</script>