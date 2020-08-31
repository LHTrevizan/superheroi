<?php

use Adianti\Database\TTransaction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;

class listahabilidadesherois extends TPage
{

    private $datagrid;

    use Adianti\Base\AdiantiStandardListTrait;


    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('mydb'); // chamando o metodo p saber o banco 
        $this->setActiveRecord('herois'); // qual a classe. 
        $this->addFilterField('id', '=', 'id'); // qual os filtros, banco like formulario 
        $this->setDefaultOrder('id', 'asc'); // ordem padrão;

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid); // para deixar com cara de boostrap
        $this->datagrid->disableDefaultClick(); // para que desabilite a função de clicar em qualquer parte da linha e ter a ação 
        $this->datagrid->width = '100%';


        //classe/tabela/campo , nome na tela, localização da palavra, tamanho.    
        $col_id                = new TDataGridColumn('id',                             'Cód',                   'right',  '10%'); //classe/tabela/campo , nome na tela, localização da palavra, tamanho. 
        $col_nome_heroi        = new TDataGridColumn('nome_heroi',             'herois',                'center', '50%');
        //$col_habilidade        = new TDataGridColumn ('habilidades->nome_habilidade',   'habilidades' ,          'center, 50%'  );
        $col_pais              = new TDataGridColumn('pais',                   'herois',                 'center,20%');




        //adicionando ordenação
        //$col_id->setAction              (new TAction([$this,'onReload']),   ['order' => 'id']);
        //$col_habilidade->setAction      (new TAction([$this,'onReload']),   ['order' => 'nome_animal']);

        //adicionando colunas
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome_heroi);
        //$this->datagrid->addColumn($col_habilidade);
        $this->datagrid->addColumn($col_pais);

        $habilidadesview = new TDataGridAction([$this, 'HabilidadesView']);
        $habilidadesview->setLabel('Ver Habilidades');
        $habilidadesview->setImage('fa:search blue fa-lg');
        $habilidadesview->setField('id');
        $this->datagrid->addAction($habilidadesview);



        $action1 = new TDataGridAction(['cadastroheroi', 'onEdit'], ['key' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}']);

        $this->datagrid->addAction($action1, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($action2, 'Excluir', 'fa:trash-alt red');
        // criando um input de busca 
        $input_busca = new TEntry('input_busca');
        $input_busca->placeholder = 'Buscar pelo heroi'; // textinho fantasma 
        $input_busca->setSize('100%');

        $this->datagrid->enableSearch($input_busca, 'id, nome_heroi'); // habilitando a busca 
        //criando a paginação 
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $panel = new TPanelGroup('Consulta'); //criando o •painel
        $panel->addHeaderWidget($input_busca); //metodo addHeaderWidget coloca a caixa de busca do lado direito. 
        $panel->add($this->datagrid); //data grid dentro do panel*/
        $panel->addFooter($this->pageNavigation);
        $this->datagrid->createModel();



        //criando uma box vertical e colocando o painel dentro dela. 
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        $vbox->add($panel);

        parent::add($vbox);
    }
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }



    public function HabilidadesView($param)
    {
        TTransaction::open('mydb');


        $hab = heroishabilidades::where('herois_id', '=', $param['id'])->load();


        if ($hab) {
            $habilidades = array();

                foreach ($hab as $itens) {
                    echo $itens->habilidades->nome_habilidade . "<br>";
                }
            

            TTransaction::close();


            new TMessage('info', $hab);
        }
    }
}
