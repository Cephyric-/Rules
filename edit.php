<?php
	require_once( 'conn.php' );
	
	$type = mysqli_real_escape_string( $con, $_GET[ 'type' ] );
	$part = mysqli_real_escape_string( $con, $_GET[ 'part' ] );
	$id = mysqli_real_escape_string( $con, $_GET[ 'id' ] );
	if( isset( $_GET[ 'new' ] ) ) $new = mysqli_real_escape_string( $con, $_GET[ 'new' ] );
	if( isset( $_GET[ 'lang' ] ) ) $lang = mysqli_real_escape_string( $con, $_GET[ 'lang' ] );
	
	if( $type == 'delete' ) {
		// Delete request
		if( $part == 'rule' ) {
			// Rule delete request
			$string = split( '-', $id );
			$cat = ( int )$string[ 0 ] - 1;
			$rule = ( int )$string[ 1 ] - 1;
			mysqli_query( $con, 'CALL DeleteRule( \'' . $cat . '\', \'' . $rule . '\' )' ) or die( 'Query fail: ' . mysqli_error( $con ) );
			echo 'Rule successfully deleted';
		} else if( $part == 'cat' ) {
			// Category delete request
			$cat = ( int )$id - 1;
			mysqli_query( $con, 'CALL DeleteCategory( \'' . $cat . '\' )' ) or die( 'Query fail: ' . mysqli_error( $con ) );
			echo 'Category successfully deleted';
		}
	} else if( $type == 'edit' ) {
		// Edit request
		if( $part == 'rule' ) {
			// Rule edit request
			$string = split( '-', $id );
			$cat = ( int )$string[ 0 ];
			$rule = ( int )$string[ 1 ];
			mysqli_query( $con, 'CALL EditRule( \'' . $cat . '\', \'' . $rule . '\', \'' . $lang . '\', \'' . htmlentities( $new, ENT_NOQUOTES, "ISO-8859-1", false ) . '\' )' ) or die( 'Query fail: ' . mysqli_error( $con ) );
			echo 'Rule successfully edited';
		} else if( $part == 'cat' ) {
			mysqli_query( $con, 'CALL EditCategory( \'' . $id . '\', \'' . $lang . '\', \'' . htmlentities( $new, ENT_NOQUOTES, "ISO-8859-1", false ) . '\' )' ) or die( 'Query fail: ' . mysqli_error( $con ) );
			echo 'Category successfully edited';
		}
	}
	
	/*function DeleteType( $part, $id ) {
		if( $part == 'cat' ) {
			// Delete specificied cat --
			// Remove 1 from every cat below it --
			// Delete any in cat_text that referenced deleted cat
			// Delete any in rule_text that referenced deleted cat
		} else {
			// Delete specificed rule
			// Delete any is rule_text that referenced deleted rule
			// Remove 1 from every rule below it
			// Update time edited
			// update rule_text where any reference to any updated rules
		}
	}*/
?>