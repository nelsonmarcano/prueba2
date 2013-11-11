<h2>Editar Permiso</h2>

<form name="form1" id="form1" method="post" action="">
    <input type="hidden" name="guardar" value="1">
    <p>
    Nombre del permiso:
    <input type="text" name="permiso" id="permiso" value="{if isset($datos.permiso)} {$datos.permiso} {/if}" />
    </p>
    <p>
    Key:
    <input type="text" name="llave" id="llave" value="{if isset($datos.key)} {$datos.key} {/if}" />
    </p>
    <input type="submit" value="Guardar" />
</form>