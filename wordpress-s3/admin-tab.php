<?php
function print_file_type( $contentType, $file ) {
	if ( ereg( 'octet-stream', $contentType ) ) {
		return ereg_replace( '(.*)\.([a-zA-Z0-9]*)$', '\\2', $file );
	} else {
		return ereg_replace( 'vnd.', '', ereg_replace( '/', ' ', $contentType ) );
	}
}
?>

<div id="amazon-s3-wrap">

<div class="infobar">

<span class="nav-controls">
<?php if ( true || $_SERVER['HTTP_REFERER'] ):?>
<a href="javascript:history.back()" onclick="" id="btn-back" title="back">back</a>
<a href="javascript:history.forward()" onclick="" id="btn-forward" title="forward">forward</a>
<?php endif; ?>
<a href="javascript:window.location.reload()" onclick="" id="btn-refresh" title="refresh view">refresh</a>
<a href="#" onclick="return s3_toggleUpload();" id="btn-upload" title="upload">upload</a>
<a href="#" onclick="return s3_toggleCreateFolder();" id="btn-folder" title="create new folder">new folder</a>
</span>
<span class="path">
<?php
$tree = $keys;
echo '<a href="'.add_query_arg( 'prefix', urlencode( '' ), $_SERVER['REQUEST_URI'] ).'" class="home">home</a> / ';
$path = '';
$paths = preg_split( '/\//', $prefix, 100, PREG_SPLIT_NO_EMPTY );
$numPaths = count( $paths );
$i=0;
foreach ( $paths as $name ) if ( $name ) {
		$path .= $name .'/';
		$isLast = ( ++$i ) >= $numPaths;
		echo '<a class="'.( $isLast ? 'last' : '' ).'" href="'.add_query_arg( 'prefix', urlencode( $path ), $_SERVER['REQUEST_URI'] ).'">'.$name.'</a> '.( !$isLast ? ' / ' : ' ' );
	}
?>
</span>
<span class="options">
<input type="checkbox" name="useBittorrent" id="useBittorrent" value="1" /><label for="useBittorrent"> create links as torrents</label>
</span>
<div id="create-form">
	<form method="post">
		<input type="hidden" name="prefix" value="<?php echo htmlentities( $_GET['prefix'] );?>">
		<input type="text" name="newfolder" id="newfolder" />
		<input type="submit" value="create" />
	</form>
</div>
<div id="upload-form">
	<form method="post" enctype="multipart/form-data">
	<input type="file" name="newfile" />
	<input type="submit" value="upload" />
	</form>
</div>

</div>
<div class="folders">
<form method="post">
<ul id="prefixes">
<?php
if ( is_array( $prefixes ) ) foreach ( $prefixes as $prefix ):
		$label = substr( $prefix, strrpos( trim( $prefix, '/' ), '/' )+1 );
	$label = ( $path ? ereg_replace( $path, "", '/'.$prefix ) : $prefix );
$label = trim( $prefix, '/' );
if ( ereg( '/', $label ) ) $label = substr( $label, strrpos( $label, '/' )+1 );
?>
<li><a href="<?php echo add_query_arg( 'prefix', urlencode( $prefix ), $_SERVER['REQUEST_URI'] );?>" title="<?php echo $label;?>"><?php echo $label;?></a></li>
<?php
endforeach;
?>
</ul>
</form>
</div>
<div class="files">
<ul id="keys">
<?php if ( count( $keys ) ): foreach ( $keys as $i => $file ):
		$file = '/'.$file;
	$url = 'http://'.$accessDomain.$file;
$label = substr( $file, strrpos( $file, '/' )+1 );
?>
<li class="<?php echo print_file_type( $meta[$i]['content-type'], $file );?>"><a
<?php if ( ereg( "^image/.*", $meta[$i]['content-type'] ) ):?>
onclick="return s3_insertImage('<?php echo $url;?>', '<?php echo basename( $url );?>')"
<?php else:?>
onclick="return s3_insertLink('<?php echo addslashes( $label );?>', '<?php echo $url;?>')"
<?php endif;?>
href="<?php echo $url;?>"
class="file <?php echo ereg_replace( '/', ' ', $meta[$i]['content-type'] );?>"
title="<?php echo $meta[$i]['date'];?> - <?php echo $meta[$i]['content-length'];?> bytes"><?php echo $label;?></a>
</li>
<?php endforeach;?>
<?php else:?>
	<li class="empty">no files in this folder</li>
<?php endif;?>
</ul>
</div>
</div>
<br clear="both" />
