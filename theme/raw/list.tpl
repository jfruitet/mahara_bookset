{foreach from=$chklist.data item=chklist}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/bookset/bookset.php?id={$chklist->id}">{$chklist->title}</a></h3>
			<div class="detail">{$chklist->description|clean_html|safe}</div>
			<div class="detail">{$chklist->value|clean_html|safe}</div>
			<div class="detail">{$chklist->threshold|clean_html|safe}</div> </p>
            <div class="fr booksettatus">			
                <a href="{$WWWROOT}artefact/bookset/edit/index.php?id={$chklist->id}" title="{str tag=edit}" >
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$chklist->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/bookset/bookset.php?id={$chklist->id}" title="{str tag=manageitems section=artefact.bookset}">
                    <img src="{theme_url filename='images/btn_configure.png'}" alt="{str(tag=manageitemssspecific section=artefact.bookset arg1=$chklist->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/bookset/delete/index.php?id={$chklist->id}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$chklist->title)|escape:html|safe}"></a>
            </div>

            <div class="cb"></div>
    </div>
{/foreach}
