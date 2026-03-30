<script type="text/x-template" id="tabla-template">
    <div class="table-contenedor">
        <div class="ui form formFiltro">
            <table class="ui table striped sortable small compact collapsing celled unstackable" v-show="!cargando">
                <thead class="center aligned">
                    <tr>
                        <th v-for="(k,v) in cols" v-on:click="ordenarPor(k)" class="sorted" :class="{ascending: orden[k] == 'asc', descending: orden[k] == 'desc'}">
                            <% v.tit | capitalize %>
                        </th>
                        <th><i class="icon setting"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td v-for="(k,v) in cols">
                            <input v-if="v.tipo == 'texto'" type="text" v-model="filtro[k]">
                            <div v-if="v.tipo == 'bool'" class="ui toggle checkbox" v-check="filtro[k]">
                                <input type="checkbox" name="public">
                            </div>
                            <select v-if="v.tipo == 'sel'" class="ui search selection dropdown fluid" v-select="filtro[k]">
                                <option value="">TODO</option>
                                <option v-for="(kk,vv) in v.op" class="item" value="<% kk %>"><% vv %></option>
                            </select>
                            <input v-if="v.tipo == 'fecha'" type="date" v-model="filtro[k]">
                            <input v-if="v.tipo == 'rango'" type="text" v-model="filtro[k]['desde']" placeholder="desde">
                            <input v-if="v.tipo == 'rango'" type="text" v-model="filtro[k]['hasta']" placeholder="hasta">
                            <input v-if="v.tipo == 'rangoFecha'" type="date" v-model="filtro[k]['desde']" placeholder="desde">
                            <input v-if="v.tipo == 'rangoFecha'" type="date" v-model="filtro[k]['hasta']" placeholder="hasta">
                        </td>
                        <td>
                            <button type="button" class="ui button mini red icon" v-on:click="quitarFiltro()"><i class="icon trash"></i></button>
                        </td>
                    </tr>
                    <tr v-for="d in datos | filterBy filterKey">
                        <td v-for="(k,v) in cols" class="<% v.align %> aligned">
                            <span v-show="v.tipo == 'texto' || v.tipo == 'rango'"><% d[k] %></span>
                            <span v-show="v.tipo == 'moneda'"><% d[k] | moneda %></span>
                            <span v-show="v.tipo == 'fecha'"><% d[k] | fecha %></span>
                            <span v-show="v.tipo == 'fechaHora'"><% d[k] | fechaHora %></span>
                            <span v-show="v.tipo == 'rangoFecha'"><% d[k] | fecha %></span>
                            <span v-show="v.tipo == 'hora'"><% d[k] | hora %></span>
                            <span v-show="v.tipo == 'sel'"><% v.op[d[k]] %></span>
                            <span v-show="v.tipo == 'archivo'"><a href="<% urlArchivo %>/<% d[k] %>"><% d[k] %></a></span>
                            <span v-show="v.tipo == 'bool'">
                                <i class="icon check green" v-show="d[k] === 1 || d[k] === 'true'"></i>
                                <i class="icon red remove" v-show="d[k] === 0 || d[k] === 'false'"></i>
                            </span>
                        </td>
                        <td class="center aligned">
                            <a type="button" v-if="urlEdit" href="<% urlEdit %>/<% d.id %>" class="ui button mini green icon"><i class="icon edit"></i></a>
                            <a type="button" v-if="urlVer" href="<% urlVer %>/<% d.id %>" class="ui button mini blue icon"><i class="icon unhide"></i></a>
                            <a type="button" v-if="urlDescarga" href="<% urlDescarga %>/<% d.id %>" class="ui button mini blue icon"><i class="icon cloud download"></i></a>
                            <button type="button" v-if="urlElim" v-on:click="confirmaElim($index,d.id)" class="ui button mini red icon"><i class="icon trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="carga" v-show="cargando">
                <i class="icon loading refresh big"></i>
                <p>Cargando Datos</p>
            </div>
        </div>
    </div>
    <div class="ui small pagination menu">
        <a class="item" v-show="current_page != 1" v-on:click="inicio()">Inicio</a>
        <a class="icon item" v-show="current_page != 1" v-on:click="anterior()">
            <i class="left arrow icon"></i>
        </a>

        <div class="item">
            Página <% current_page %> de <% last_page %>
        </div>
        <div class="item">
            <div class="ui category search item">
                <div class="ui transparent icon input">
                    <input class="prompt" type="text" placeholder="Ir a pagina..." v-on:change="irPagina()" v-model="irA">
                    <i class="search link icon"></i>
                </div>
            </div>
        </div>
        <a class="icon item" v-show="current_page != last_page" v-on:click="siguiente()">
            <i class="right arrow icon"></i>
        </a>
        <a class="item" v-show="current_page != last_page" v-on:click="fin()">Fin</a>
    </div>
    <div class="ui small modal modalEliminar">
        <i class="close icon"></i>

        <div class="header">CONFIRMAR ELIMINACIÓN</div>
        <div class="content">
            <div class="description">¿Confirma la eliminación del registro?</div>
        </div>
        <div class="actions">
            <div class="ui button negative">NO</div>
            <button class="ui button green" v-on:click="eliminar()">SI</button>
        </div>
    </div>
</script>