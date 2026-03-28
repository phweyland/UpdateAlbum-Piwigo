<?php
defined('UPDATEALBUM_PATH') or die('Hacking attempt!');

global $conf, $template, $page, $user;

$base_url = get_root_url().'admin.php?page=';

// +-----------------------------------------------------------------------+
// | specific actions                                                      |
// +-----------------------------------------------------------------------+
if (isset($_GET['action'])) {
  if ('empty_caddie' == $_GET['action']) {
    $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
    pwg_query($query);
    redirect(UPDATEALBUM_ADMIN);
  }
}

// save config
if (isset($_POST['save']))
{
	$conf['updatealbum']['update'] = isset($_POST['updateFlag']);
	$conf['updatealbum']['create'] = isset($_POST['createFlag']);
	$conf['updatealbum']['addtocaddie'] = isset($_POST['addtocaddieFlag']);
	$conf['updatealbum']['verbose'] = isset($_POST['verboseFlag']);
	if (isset($_POST['parent_cat'])) $conf['updatealbum']['parent_cat'] = $_POST['parent_cat'];

	conf_update_param('updatealbum', $conf['updatealbum']);
}
// check category
if (!empty($conf['updatealbum']['parent_cat'])) {
	$query = '
	SELECT *
	  FROM '.CATEGORIES_TABLE.'
	  WHERE id = '.$conf['updatealbum']['parent_cat'].'
	;';
	$category = pwg_db_fetch_assoc(pwg_query($query));
}
// Treatment
if (isset($_POST['update']) and isset($_FILES['imagesfiles']))
{
	include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

	if ($_FILES['imagesfiles']['error'][0] == UPLOAD_ERR_NO_FILE) {
//		$page['infos'][] = l10n('No file selected');
	}
	elseif (empty($category['id'])) {
		$page['errors'][] = l10n('No album selected');
	}
	else {
		// number of files to be uploaded
		$nbFiles = count($_FILES['imagesfiles']['name']);
		$page['infos'][] = $nbFiles.' '.l10n('Selected Files');
		if ($conf['updatealbum']['verbose']) {
			$query = '
			SELECT ic.name
			  FROM '.CATEGORIES_TABLE.' AS ic
			  WHERE ic.id = '.$category['id'].'
			;';
			$parent_cat = pwg_db_fetch_assoc(pwg_query($query));
			$page['infos'][] = l10n('Selected Album').': '.$parent_cat['name'].' -> '.$category['id'];
		}
		$nbUpdated = 0;
		$nbCreated = 0;
		$nbErrors = 0;
		$inserts = array();

		// list all files in this album
		$query = '
SELECT
    i.id,
    i.file
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON image_id = i.id
  WHERE ic.category_id = '.$category['id'].'
;';
		$cat_files = query2array($query, 'file', 'id');

		for($i=0; $i<$nbFiles; $i++) {
			if ($_FILES['imagesfiles']['error'][$i] !== UPLOAD_ERR_OK) {
				$error_message = file_upload_error_message($_FILES['imagesfiles']['error'][$i]);
				$page['infos'][] = $_FILES['imagesfiles']['name'][$i].' : '.l10n('Error').' -> '.$error_message;
				$nbErrors++;
			}
			elseif (!in_array(strtolower(get_extension($_FILES['imagesfiles']['name'][$i])), array('jpg', 'jpeg', 'png', 'gif')))  {
				$page['infos'][] = $_FILES['imagesfiles']['name'][$i].' : '.l10n('Error').' -> '.l10n('Unsupported file extension');
				$nbErrors++;
			}
			else {
			// identify the corresponding image in the album
				$image = null;
				if (isset($cat_files[ $_FILES['imagesfiles']['name'][$i] ]))
				{
					$image = array('id' => $cat_files[ $_FILES['imagesfiles']['name'][$i] ]);
				}

				if (!empty($image)) { // if image found
					if ($conf['updatealbum']['update']) { // if update flag then update it
						add_uploaded_file(
							$_FILES['imagesfiles']['tmp_name'][$i],
							$_FILES['imagesfiles']['name'][$i],
							null,
							null,
							$image['id']
							);
						if ($conf['updatealbum']['verbose']) $page['infos'][] = $_FILES['imagesfiles']['name'][$i].' : '.l10n('Updated image').' -> '.$image['id'];
						$nbUpdated++;
						}
					else {
						if ($conf['updatealbum']['verbose']) $page['infos'][] = $_FILES['imagesfiles']['name'][$i].' : '.l10n('Not updated image').' -> '.$image['id'];
					}
				}
				else { // if image not found
					if ($conf['updatealbum']['create']) { // if create flag then create it
						$image['id'] = add_uploaded_file(
							$_FILES['imagesfiles']['tmp_name'][$i],
							$_FILES['imagesfiles']['name'][$i],
							array($category['id'])
							);

							$inserts[] = array(
								'user_id' => $user['id'],
								'element_id' => $image['id'],
							);

						if ($conf['updatealbum']['verbose']) $page['infos'][] = $_FILES['imagesfiles']['name'][$i].'  : '.l10n('Added image').' -> '.$image['id'];
						$nbCreated++;
					}
					else {
						if ($conf['updatealbum']['verbose']) $page['infos'][] = $_FILES['imagesfiles']['name'][$i].' : '.l10n('Not added image');
					}
				}
			}
		}
		$page['infos'][] = $nbUpdated.' '.l10n('Updated images').'; '.$nbCreated.' '.l10n('Created images').'; '.$nbErrors.' '.l10n('Error(s)');
    empty_lounge(true);
		if ($conf['updatealbum']['addtocaddie'] and $nbCreated) {
			 mass_inserts(
				CADDIE_TABLE,
				array_keys($inserts[0]),
				$inserts
			);
		}
	}
}
// Display links if any
if (isset($category['id'])) {
	// Category name
	if (function_exists('get_extended_desc')) {
		$categoryName = get_extended_desc($category['name']);
	} else {
		$categoryName = $category['name'];
	}

	// public album link
	$template->assign(
	array(
		'U_JUMPTO' => make_index_url(array('category' => $category)),
		)
	);

	$query = 'SELECT count(*) as nblines
	  FROM '.IMAGE_CATEGORY_TABLE.'
	  WHERE category_id = '.$category['id'].'
	  ;';
	$result = pwg_db_fetch_assoc(pwg_query($query));
	$nbImagesCategory = $result['nblines'];
	$category['has_images'] = $nbImagesCategory>0 ? true : false;
	// photos management link
	$base_url = get_root_url().'admin.php?page=';
	if ($category['has_images'])
	{
	  $template->assign(
	  array (
		'CAT_ID'            => $category['id'],
		'CAT_ADMIN_ACCESS' => cat_admin_access($category['id']),
		'U_MANAGE_ELEMENTS' => $base_url.'batch_manager&amp;filter=album-'.$category['id'],
		'nb_category' => $nbImagesCategory,
		)
	  );
	}
	// album properties link
	$template->assign(array(
	  'url_albumproperties' => $base_url.'album-'.$category['id'],
	  'category_name' => $categoryName,
	  ));

	 $query = '
	SELECT count(*) as nblines FROM '.CADDIE_TABLE.'
	  WHERE user_id = '.$user['id'].'
	;';
	$result = pwg_db_fetch_assoc(pwg_query($query));
	$nbImagesCaddie = $result['nblines'];

	// caddie links
	if ($conf['updatealbum']['create'] and $conf['updatealbum']['addtocaddie']) {
	$template->assign(array(
	  'url_caddie' => get_root_url().'admin.php?page=batch_manager&filter=prefilter-caddie',
	  'nb_caddie' => $nbImagesCaddie,
	  'url_emptycaddie' => UPDATEALBUM_ADMIN.'&action=empty_caddie',
	  ));
	}
}

// categories selector
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper(
  $query,
  empty($conf['updatealbum']['parent_cat']) ? array() : array($category['id']),
  'category_parent_options'
  );

// send config to template
$template->assign(array(
  'updatealbum' => $conf['updatealbum'],
  ));

// define template file
$template->set_filename('updatealbum_content', realpath(UPDATEALBUM_PATH . 'admin/template/config.tpl'));
