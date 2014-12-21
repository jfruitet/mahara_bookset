
{foreach from=$booksets.data item=bookset}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset->id}">{$bookset->title}</a></h3>
 
            <div class="fr booksetstatus">
                <a href="{$WWWROOT}artefact/bookset/export/index.php?id={$bookset->id}" title="{str tag=select}" >
                    <img src="{theme_url filename='images/btn_configure.png'}" alt="{str(tag=editspecific arg1=$bookset->title)|escape:html|safe}"></a>
             </div>

			<div class="detail">{$bookset->description|clean_html|safe} {$bookset->motivation|clean_html|safe}</div>

            <div class="cb"></div>
    </div>
{/foreach}
