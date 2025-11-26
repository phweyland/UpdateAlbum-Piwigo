{* {combine_css path=$UPDATEALBUM_PATH|@cat:"admin/template/style.css"} *}

{html_style}
  h4 {
    text-align:left !important;
  }
{/html_style}


<div class="titrePage">
	<h2>{'Update Album'|translate}</h2>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend>{'Configuration'|translate}</legend>
<ul>
  <li>
    <input type="checkbox" id="update" name="updateFlag"{if $updatealbum.update} checked="checked"{/if}>
    <label>
      {'Update existing images'|@translate}
    </label>
  </li>
  <li>
    <input type="checkbox" id="create" name="createFlag"{if $updatealbum.create} checked="checked"{/if}> 
    <label>
      {'Create images if not exist'|@translate}
    </label>
{if $updatealbum.create} 
	(<input type="checkbox" id="addtocaddie" name="addtocaddieFlag"{if $updatealbum.addtocaddie} checked="checked"{/if}>
	<label>
	  {'Add new images to caddie'|@translate}
	</label>)
{/if}
  </li>
  <li>
    <input type="checkbox" id="verbose" name="verboseFlag"{if $updatealbum.verbose} checked="checked"{/if}>
    <label>
      {'Display detailed actions done'|@translate}
    </label>
  </li>
  <li>
    <label>
      {'Select Album to be updated'|@translate}
    </label>
    <select class="categoryDropDown" id="parent_cat" name="parent_cat">
      <option value="0">------------</option>
      {html_options options=$category_parent_options selected=$category_parent_options_selected}
    </select>
  </li>
 </ul>  
 
<p class="formButtons">
  <input class="submit" type="submit" value="{'Save'|@translate}" name="save">
</p>
</fieldset>
</form>
  
<form method="post" action="" class="properties" enctype="multipart/form-data">
<fieldset>
  <legend>{'Update Album'|translate}</legend>
<ul>
  <li>
      {'Select files'|@translate} 
      <input type="file" name="imagesfiles[]" multiple />
	  {'Allowed file types'|translate}: jpg, jpeg, png & gif.
  </li>
 </ul>  
 
<p class="formButtons">
  <input class="submit" type="submit" value="{'Update'|@translate} {if isset($category_name)}&quot;{$category_name}&quot;{/if}" name="update" {if !isset($category_name)}Disabled{/if}>
</p>
</fieldset>
</form>


{if isset($category_name)}
<fieldset>
  <legend>{'Links'|@translate}</legend>
<ul style="list-style: none;">
{if isset($U_MANAGE_ELEMENTS) }
  <li><a class="icon-picture" href="{$U_MANAGE_ELEMENTS}">{'manage album photos'|@translate} "{$category_name}" ({$nb_category})</a></li>
{/if}
{if isset($url_albumproperties) }
  <li><a class="icon-pencil" href=" {$url_albumproperties}">{'Edit'|@translate}</a></li>
{/if}
{if isset(CAT_ADMIN_ACCESS) and $CAT_ADMIN_ACCESS}
  <li><a class="icon-eye" href="{$U_JUMPTO}">{'jump to album'|@translate} →</a></li>
{/if}
{if isset($url_caddie) }
  <li></br></li>
  <li><a class="icon-flag" href="{$url_caddie}">{'Manage caddie\'s photos'|@translate} ({$nb_caddie})</a></li>
  <li><a class="icon-cancel" href="{$url_emptycaddie}">{'Empty caddie'|@translate}</a></li>
{/if}

</ul> 

</fieldset>
{/if}

