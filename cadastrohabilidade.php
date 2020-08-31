<?php

use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Wrapper\TDBCombo;

class cadastrohabilidade extends TPage
{

    private $form;


    public function __construct()
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle("Cadastro do poder");
        $this->form->generateAria();

        $this->form->setClientValidation(true);
        //variavel- banco de dadps - classe/tabela - campo chave - atributo (Campo visivel p usuario). 
        $id                  = new TDBEntry('id',             'mydb', 'habilidades', 'id', 'id');
        $nome_habilidade     = new TDBEntry('nome_habilidade','mydb', 'habilidades', 'id', 'nome_habilidade');
        //$heroi_id            = new TDBCombo('heroi_id',       'mydb', 'herois', 'id', 'nome_heroi');


        $id->setEditable(FALSE); // desliga o campo,deixa intocavel para o usuario.
       


        $this->form->appendPage('cadastro do heroi');
        $this->form->addFields([new TLabel('<b>id</b>')],                [$id]);
        $this->form->addFields([new TLabel('<b>Nome da habilidade</b>')],[$nome_habilidade]);
        //$this->form->addFields([new TLabel('<b>Heroi</b>')],             [$heroi_id]);
        

        $nome_habilidade ->addValidation('nome_habilidade ', new TRequiredValidator);
       


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

            $habilidades = new habilidades;
            $habilidades->fromArray((array) $data);
            $habilidades->store();
            $data->id = $habilidades->id;
            $this->form->setData($data);

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

            if (isset($param['id'])) {
                $id = $param['id'];
                $habilidades = new habilidades($id);
                $this->form->setData($habilidades);
            } else {
                $this->form->clear(true);
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
