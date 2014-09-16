<?php
	require_once( 'conn.php' );
	
	
?>
<html>
	<head>
		<style>
			body { background: #f6f6f6 }
			body, section, ol, p {
				margin: 0;
				padding: 0;
				font: 16px Verdana;
			}
			.buttons {
				float: right;
				width: 60px;
				height: 25px;
				text-align: center;
			}
			.cat-name, .cat { font-weight: bold }
			.cat-name {
				float: left;
			}
			.rule, .cats { min-height: 25px }
			.rule-name {
				float: left;
				padding-right: 130px;
				width: 80%;
				min-height: 25px;
			}
			.clear:after {
				clear: both;
				content: "";
				display: table;
			}
			ol { counter-reset: li }
			ol > li {
				position: relative;
				margin: 0 0 0 2em;
				padding: 4px 0 0 8px;
				list-style: none;
				border-top: 2px solid #666;
				background: #f6f6f6;
			}
			ol > li:before {
				content: counter( li );
				counter-increment: li;
				position: absolute;
				top: -2px;
				left: -32px;
				box-sizing: border-box;
				width: 32px;
				height: 32px;
				margin-right: 8px;
				padding: 4px;
				border-top: 2px solid #666;
				color: #fff;
				background: #666;
				font-weight: bold;
				text-align: center;
			}
			li ol {
				margin-left: -40px;
				padding-left: 32px;
			}
			li { cursor: pointer }
			ol ol li, ol ol { background: #e6e6e6 }
			ol ol li:last-child { margin-bottom: 0 }​
		</style>
		<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
		<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.min.js"></script>
		<script>
			$( document ).ready( function() {
				GetRuleSets();
				setTimeout( function() { ShowRuleSets( 'English' ) }, 100 );
			} );
			
			var languages = [];
			var deleted = [];
			
			function GetRuleSets() {
				$.get( 'rules.php', function( data ) { languages = data } );
			}
			
			function ShowRuleSets( lang ) {
				document.getElementById( 'wrapper' ).className = lang;
				
				var rulelist = document.createElement( 'ol' );
				document.getElementById( 'wrapper' ).appendChild( rulelist );
				
				$( rulelist ).sortable({
					axis: 'y'
				} );
				
				// Categories
				for ( var i in languages[ lang ] ) {
					var catrow = document.createElement( 'li' );
					catrow.id = 'cat' + ( Number( i ) + 1 );
					rulelist.appendChild( catrow );
					
					// Category wrap
					var catwrap = document.createElement( 'section' );
					catwrap.classname = 'cats';
					catwrap.style.height = '25px';
					catrow.appendChild( catwrap );
					
					// Category name
					var catname = document.createElement( 'aside' );
					catname.className = 'cat-name';
					catname.innerHTML = languages[ lang ][ i ].name;
					catwrap.appendChild( catname );
					
					// Category edit
					var catedit = document.createElement( 'section' );
					catedit.className = 'buttons cat';
					catedit.onclick = function() {
						var editwrap = document.createElement( 'section' );
						editwrap.className = 'edit-dialogue';
						document.getElementById( 'wrapper' ).insertBefore( editwrap, document.getElementById( 'wrapper' ).firstChild );
						
						var edittitle = document.createElement( 'p' );
						edittitle.innerHTML = 'Editing category ' + $( this ).parent().parent().attr( 'id' ).substr( 3, ( $( this ).parent().parent().attr( 'id' ) ).length );
						editwrap.appendChild( edittitle );
						
						var editform = document.createElement( 'form' );
						editform.id = 'editform';
						editform.onsubmit = function( event ) {
							event.preventDefault();
							
							var objectid = $( '#editform input[ name=meta ]' ).val();
							$.each( $( '#editform input[ type=text ]' ), function( k, v ) {
								if( v.value != languages[ v.name ][ Number( objectid ) ].name ) {
									$.ajax( {
										url: 'edit.php?type=edit&part=cat&id=' + objectid + '&lang=' + v.name + '&new=' + v.value,
										error: function( jqXHR, textStatus, errorThrown ) {
											alert( 'An error occured: ' + textStatus );
										},
										success: function( data ) {
											console.log( data );
											languages[ v.name ][ Number( objectid ) ].name = v.value;
											if( $( '#wrapper' ).hasClass( v.name ) ) $( '#' +  $( '#editform input[ name=type ]' ).val() + ( ( Number( objectid ) + 1 ) + ' .cat-name' ) ).html( v.value );
										}
									} );
								}
							} );
							
							
							$.each( $( '#editform input[ type=text ]' ), function( k, v ) {
								if( v.value != languages[ v.name ][ $( '#editform input[ name=meta ]' ).val() ].name ) {
									languages[ v.name ][ $( '#editform input[ name=meta ]' ).val() ].name = v.value;
									deleted.push( { 'type': 'edit', 'part': 'cat', 'lang': v.name, 'id': $( '#editform input[ name=meta ]' ).val(), 'value': v.value } );
									if( $( '#wrapper' ).hasClass( v.name ) ) $( '#' +  $( '#editform input[ name=type ]' ).val() + ( Number( $( '#editform input[ name=meta ]' ).val() ) + 1 ) + ' .cat-name' ).html( v.value );
									
									// TODO: Show apply changes button if not already there
								}
							} );
							
							$( '.edit-dialogue' ).remove();
						}
						editwrap.appendChild( editform );
						
						for( var ln in languages[ 'languages' ] ) {
							var editformlabel = document.createElement( 'label' );
							editformlabel.setAttribute( 'for', languages[ 'languages' ][ ln ] );
							editformlabel.innerHTML = languages[ 'languages' ][ ln ] + ': ';
							editform.appendChild( editformlabel );
							
							var editformlang = document.createElement( 'input' )
							editformlang.type = 'text';
							editformlang.name = languages[ 'languages' ][ ln ];
							editformlang.value = languages[ languages[ 'languages' ][ ln ] ][ Number( ( $( this ).parent().parent().attr( 'id' ) ).substr( 3, ( $( this ).parent().parent().attr( 'id' ) ).length ) ) - 1 ].name;
							editform.appendChild( editformlang );
							
							var editformbr = document.createElement( 'br' );
							editform.appendChild( editformbr );
						}
						
						var editformtype = document.createElement( 'input' );
						editformtype.name = 'type';
						editformtype.type = 'hidden';
						editformtype.value = 'cat';
						editform.appendChild( editformtype );
						
						var editformmeta = document.createElement( 'input' );
						editformmeta.name = 'meta';
						editformmeta.type = 'hidden';
						editformmeta.value = Number( ( $( this ).parent().parent().attr( 'id' ) ).substr( 3, ( $( this ).parent().parent().attr( 'id' ) ).length ) ) - 1;
						editform.appendChild( editformmeta );
						
						var editformsubmit = document.createElement( 'input' );
						editformsubmit.type = 'submit';
						editformsubmit.value = 'Submit';
						editform.appendChild( editformsubmit );
						
						var editformcancel = document.createElement( 'button' );
						editformcancel.innerHTML = 'Cancel';
						editformcancel.onclick = function() {
							$( '.edit-dialogue' ).remove();
						}
						editform.appendChild( editformcancel );
						
						$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
					}
					catedit.innerHTML = 'Edit';
					catwrap.appendChild( catedit );
					
					// Category delete
					var catdelete = document.createElement( 'section' );
					catdelete.className = 'buttons cat';
					catdelete.onclick = function() {
						if( confirm( 'Are you sure you wish delete this category and all the rules inside it?' ) ) {
							var objectid = $( this ).parent().parent().attr( 'id' );
							$.ajax( {
								url: 'edit.php?type=delete&part=cat&id=' + objectid.substr( 3, ( $( this ).parent().parent().attr( 'id' ) ).length ),
								error: function( jqXHR, textStatus, errorThrown ) {
									alert( 'An error occured: ' + textStatus );
								},
								success: function( data ) {
									console.log( data );
									$( '#' + objectid ).remove();
								}
							} );
						}
					}
					catdelete.innerHTML = 'Delete';
					catwrap.appendChild( catdelete );
					
					// Rule wrapper
					var rulewrap = document.createElement( 'ol' );
					catrow.appendChild( rulewrap );
					
					$( rulewrap ).sortable({
						axis: 'y'
					} );
					
					// Rules
					for( var rule in languages[ lang ][ i ].rules ) {
						var rulerow = document.createElement( 'li' );
						rulerow.id = 'rule' + ( Number( i ) + 1 ) + '-' +( Number( rule ) + 1 );
						rulerow.className = 'rule clear';
						rulewrap.appendChild( rulerow );
						
						// Rule name TD
						var rulename = document.createElement( 'aside' );
						rulename.className = 'rule-name';
						rulename.innerHTML =  languages[ lang ][ i ].rules[ rule ].text;
						rulerow.appendChild( rulename );
						
						// Rule edit
						var ruleedit = document.createElement( 'section' );
						ruleedit.className = 'buttons';
						ruleedit.onclick = function() {
							var editwrap = document.createElement( 'section' );
							editwrap.className = 'edit-dialogue';
							document.getElementById( 'wrapper' ).insertBefore( editwrap, document.getElementById( 'wrapper' ).firstChild );
							
							var edittitle = document.createElement( 'p' );
							edittitle.innerHTML = 'Editing rule ' + ( $( this ).parent().attr( 'id' ) ).substr( 4, ( $( this ).parent().attr( 'id' ) ).length );
							editwrap.appendChild( edittitle );
							
							var editform = document.createElement( 'form' );
							editform.id = 'editform';
							editform.onsubmit = function( event ) {
								event.preventDefault();
								
								var objectid = $( '#editform input[ name=meta ]' ).val();
								$.each( $( '#editform input[ type=text ]' ), function( k, v ) {
									if( v.value != languages[ v.name ][ Number( objectid.substr( 0, 1 ) ) ].rules[ Number( objectid.substr( 2, 1 ) ) ].text ) {
										$.ajax( {
											url: 'edit.php?type=edit&part=rule&id=' + objectid + '&lang=' + v.name + '&new=' + v.value,
											error: function( jqXHR, textStatus, errorThrown ) {
												alert( 'An error occured: ' + textStatus );
											},
											success: function( data ) {
												console.log( data );
												languages[ v.name ][ Number( objectid.substr( 0, 1 ) ) ].rules[ Number( objectid.substr( 2, 1 ) ) ].text = v.value;
												if( $( '#wrapper' ).hasClass( v.name ) ) $( '#' +  $( '#editform input[ name=type ]' ).val() + ( ( Number( objectid.substr( 0, 1 ) ) + 1 ) + '-' + ( Number( objectid.substr( 2, 1 ) ) + 1 ) ) + ' .rule-name' ).html( v.value );
											}
										} );
									}
								} );
								
								$( '.edit-dialogue' ).remove();
							}
							editwrap.appendChild( editform );
							
							for( var ln in languages[ 'languages' ] ) {
								var editformlabel = document.createElement( 'label' );
								editformlabel.setAttribute( 'for', languages[ 'languages' ][ ln ] );
								editformlabel.innerHTML = languages[ 'languages' ][ ln ] + ': ';
								editform.appendChild( editformlabel );
								
								var editformlang = document.createElement( 'input' )
								editformlang.type = 'text';
								editformlang.name = languages[ 'languages' ][ ln ];
								editformlang.value = languages[ languages[ 'languages' ][ ln ] ][ Number( ( $( this ).parent().attr( 'id' ) ).substr( 4, 1 ) ) - 1 ].rules[ Number( ( $( this ).parent().attr( 'id' ) ).substr( 6, 1 ) ) - 1 ].text;
								editform.appendChild( editformlang );
								
								var editformbr = document.createElement( 'br' );
								editform.appendChild( editformbr );
							}
							
							var editformtype = document.createElement( 'input' );
							editformtype.name = 'type';
							editformtype.type = 'hidden';
							editformtype.value = 'rule';
							editform.appendChild( editformtype );
							
							var editformmeta = document.createElement( 'input' );
							editformmeta.name = 'meta';
							editformmeta.type = 'hidden';
							editformmeta.value = ( Number( ( $( this ).parent().attr( 'id' ) ).substr( 4, 1 ) ) - 1 ) + '-' + ( Number( ( $( this ).parent().attr( 'id' ) ).substr( 6, 1 ) ) - 1 );
							editform.appendChild( editformmeta );
							
							var editformsubmit = document.createElement( 'input' );
							editformsubmit.type = 'submit';
							editformsubmit.value = 'Submit';
							editform.appendChild( editformsubmit );
							
							var editformcancel = document.createElement( 'button' );
							editformcancel.innerHTML = 'Cancel';
							editformcancel.onclick = function() {
								$( '.edit-dialogue' ).remove();
							}
							editform.appendChild( editformcancel );
							
							$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
						}
						ruleedit.innerHTML = 'Edit';
						rulerow.appendChild( ruleedit );
						
						// Rule delete
						var ruledelete = document.createElement( 'section' );
						ruledelete.className = 'buttons';
						ruledelete.onclick = function() {
							if( confirm( 'Are you sure you want to delete this rule?' ) ) {
								var objectid = $( this ).parent().attr( 'id' );
								$.ajax( {
									url: 'edit.php?type=delete&part=rule&id=' + objectid.substr( 4, ( objectid ).length ),
									error: function( jqXHR, textStatus, errorThrown ) {
										alert( 'An error occured: ' + textStatus );
									},
									success: function( data ) {
										console.log( data );
										$( '#' + objectid ).remove();
									}
								} );
							}
						}
						ruledelete.innerHTML = 'Delete';
						rulerow.appendChild( ruledelete );
					}
				}
			}
			function quickscroll() {
				$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
			}
		</script>
	</head>
	<body>
		<section id="wrapper"></section>
	</body>
</html>