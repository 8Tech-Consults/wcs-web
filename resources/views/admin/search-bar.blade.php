<li>
    <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
            @if ($u->isRole('super-admin'))
                <input type="text" class="form-control" id="navbar-search-input" placeholder="Search for cases...">
            @elseif($u->isRole('admin'))
                <input type="text" class="form-control" id="navbar-search-input" placeholder="Search for a cases...">
            @else
                <input type="text" class="form-control" id="navbar-search-input" placeholder="Search...">
            @endif

        </div>
    </form>
</li>
