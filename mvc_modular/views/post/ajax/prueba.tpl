{if isset($post) && count($post)}
    <table class="table table-bordered table-condensed table-striped">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Pais</th>
                <th>Ciudad</th>
            </tr>

            {foreach item=datos from=$post}
                <tr>
                    <td>{$datos.id}</td>
                    <td>{$datos.nombre}</td>
                    <td>{$datos.pais}</td>
                    <td>{$datos.ciudades}</td>
                </tr>
            {/foreach}
        </table>
{else}

    <p><strong>No hay posts!</strong></p>

{/if}

{$paginacion|default:""}