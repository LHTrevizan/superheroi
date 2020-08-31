<?php

use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TDate;

class cadastroheroi extends TPage
{

    private $form;


    public function __construct()
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle("Cadastro do heroi");
        $this->form->generateAria();

        $this->form->setClientValidation(true);
        //variavel- banco de dadps - classe/tabela - campo chave - atributo (Campo visivel p usuario). 
        $id                  = new TDBEntry('id',        'mydb', 'herois', 'id', 'id');
        $nome_heroi          = new TDBEntry('nome_heroi', 'mydb', 'herois', 'id', 'nome_heroi');
        $pais                = new TDBEntry('pais',      'mydb', 'herois', 'id', 'pais');
        $habilidades_id     = new TDBCheckGroup('habilidades_id', 'mydb', 'habilidades', 'id', 'nome_habilidade');


        $id->setEditable(FALSE); // desliga o campo,deixa intocavel para o usuario.



        $this->form->appendPage('cadastro do heroi');
        $this->form->addFields([new TLabel('<b>id</b>')],                 [$id]);
        $this->form->addFields([new TLabel('<b>Nome do heroi</b>')],      [$nome_heroi]);
        $this->form->addFields([new TLabel('<b>País de nascimento</b>')], [$pais]);
        $this->form->addFields([new TLabel('<b>Nome das habilidade do heroi</b>')], [$habilidades_id]);


        $nome_heroi->addValidation('nome_heroi ', new TRequiredValidator);




        $this->form->addAction('Enviar',   new TAction([$this,  'onSave']), 'fa:save green');
        $this->form->addActionLink('limpar',   new TAction([$this,  'onClear']),  'fa:eraser red');

        parent::add($this->form);
    }

    public function onClear()
    {

        $this->form->clear();
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('mydb');
            $this->form->validate();

            $data = $this->form->getData();

            $herois = new herois;
            $herois->fromArray((array) $data);
            $herois->store();
            $data->id = $herois->id;

            //heroishabilidades::where('herois_id', '=', $herois->id)->delete();
            $this->form->setData($data);
            var_dump($data);
            if (!empty($param['nome_habilidade'])) {
                foreach ($param['nome_habilidade'] as $habilidades_id) {
                    $herois_habilidades = new heroishabilidades;
                    $herois_habilidades->habilidades_id = $habilidades_id;
                    $herois_habilidades->herois_id =  $herois->id;
                    $herois_habilidades->store();
                }
            }

        

            TTransaction::close();

            new TMessage('info',  'cadastrado com sucesso');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            TTransaction::open('mydb');
    
            if (isset($param['key'])) {
    
                $key = $param['key'];

                $obj = new herois($key);
    
                $object = heroishabilidades::where('herois_id','=', $key)->load();
                $chackbox = array();
                
                if($object)
                {
                    foreach( $object as $itens )
                    {
                        $chackbox[] = $itens->habilidades_id;
                        $chackbox->store();
                    }
                }
                
                $obj->habilidades_id = $chackbox;              
    
                $this->form->setData($obj);
                TTransaction::close();
            } else {
                $this->form->clear(TRUE);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}





    /*public function onEdit($param)
    {
        try {
            TTransaction::open('mydb');

            if (isset($param['id'])) {
                $id = $param['id'];
                $herois = new herois($id);
                $this->form->setData($herois);
            } else {
                $this->form->clear(true);
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }*/
