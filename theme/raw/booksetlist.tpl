{foreach from=$booksets.data item=bookset}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset->id}">{$bookset->title}</a></h3>
			
            <div class="fr booksetstatus">
                <a href="{$WWWROOT}artefact/bookset/select/bookset_select.php?id={$bookset->id}" title="{str tag=select section=artefact.bookset}" >
                    <img src="{$iconcheckpath}" alt="{str(tag=selectspecific section=artefact.bookset arg1=$bookset->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset->id}" title="{str tag=show section=artefact.bookset}" >
                    <img src="{$iconshowpath}" alt="{str(tag=show section=artefact.bookset arg1=$bookset->title)|escape:html|safe}"></a>			
                <a href="{$WWWROOT}artefact/bookset/edit/index.php?id={$bookset->id}" title="{str tag=edit}" >
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$bookset->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset->id}" title="{str tag=managecomponents section=artefact.bookset}">
                    <img src="{theme_url filename='images/btn_configure.png'}" alt="{str(tag=manageitemsspecific section=artefact.bookset arg1=$bookset->title)|escape:html|safe}"></a>
<!--                
				<a href="{$WWWROOT}artefact/bookset/export/index.php?id={$bookset->id}" title="{str tag=export section=artefact.bookset}" >
                    <img src="{theme_url filename='images/btn_export.png'}" alt="{str(tag=exportspecific  section=artefact.bookset arg1=$bookset->title)|escape:html|safe}"></a>			
-->
				<a href="{$WWWROOT}artefact/bookset/delete/index.php?id={$bookset->id}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$bookset->title)|escape:html|safe}"></a>
            
			</div>

			<table><tr>
			<td><b>{str tag='description' section='artefact.bookset'}</b> : {$bookset->description|safe}</td>
			<td><b>{str tag='statusmodif' section='artefact.bookset'}</b> : {$bookset->status|safe}</td>
			<td><b>{str tag='publicbookset' section='artefact.bookset'}</b> : {$bookset->public|safe}</td>
			<td><b>{str tag='selectedbookset' section='artefact.bookset'}</b> : {$bookset->select|safe}</td></tr></table>

            <div class="cb"></div>
    </div>
{/foreach}

