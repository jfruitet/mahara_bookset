{foreach from=$booksets.data item=bookset}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset->id}">{$bookset->title}</a></h3>
 
            <div class="fr booksetstatus">
                <a href="{$WWWROOT}artefact/bookset/edit/index.php?id={$bookset->id}" title="{str tag=edit}" >
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$bookset->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset->id}" title="{str tag=managecomponents section=artefact.bookset}">
                    <img src="{theme_url filename='images/btn_configure.png'}" alt="{str(tag=manageitemsspecific section=artefact.bookset arg1=$bookset->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/bookset/export/index.php?id={$bookset->id}" title="{str tag=export section=artefact.bookset}" >
                    <img src="{theme_url filename='images/btn_export.png'}" alt="{str(tag=exportspecific  section=artefact.bookset arg1=$bookset->title)|escape:html|safe}"></a>			
				<a href="{$WWWROOT}artefact/bookset/delete/index.php?id={$bookset->id}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$bookset->title)|escape:html|safe}"></a>
            
			</div>

			<div class="detail">{$bookset->description|safe} {$bookset->motivation|safe}</div>

            <div class="cb"></div>
    </div>
{/foreach}

