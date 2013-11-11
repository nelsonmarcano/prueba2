<h2>Editar role</h2>

<form name="form1" id="form1" method="post" action="">
    <input type="hidden" name="guardar" value="1">
    <p>
    Nombre del role:
    <input type="text" name="role" id="role" value="{if isset($datos.role)} {$datos.role} {/if}" />
    </p>
    <input type="submit" value="Editar" />
</form>