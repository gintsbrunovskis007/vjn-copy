<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="jn-search-field" class="screen-reader-text">Meklēt vietnē</label>
	<input type="search" id="jn-search-field" class="search-field" placeholder="Meklēt…" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="search-submit">Meklēt</button>
</form>
