<?php
class RegistroTopico{
    
    private $topicoConteudo;
    
    public function getTopicoConteudo() {
        return $this->topicoConteudo;
    }

    public function setTopicoConteudo($topicoConteudo) {
        $this->topicoConteudo = $topicoConteudo;
    }

    private $tutorialID;
    
    public function getTutorialID() {
        return $this->tutorialID;
    }
    public function setTutorialID($tutorialID) {
        $this->tutorialID = $tutorialID;
    }
    
    private $topicoSuperior;
    public function getTopicoSuperior() {
        return $this->topicoSuperior;
    }
    public function setTopicoSuperior($topicoSuperior) {
        $this->topicoSuperior = $topicoSuperior;
    }
    
    private $topicoID;
    public function getTopicoID() {
        return $this->topicoID;
    }    
    public function setTopicoID($topicoID) {
        $this->topicoID = $topicoID;
    }
    
    private $topicoOrdem;
    public function getTopicoOrdem() {
        return $this->topicoOrdem;
    }
    public function setTopicoOrdem($topicoOrdem) {
        $this->topicoOrdem = $topicoOrdem;
    }
    
    private $topicoNivel;
    public function getTopicoNivel() {
        return $this->topicoNivel;
    }
    public function setTopicoNivel($topicoNivel) {
        $this->topicoNivel = $topicoNivel;
    }
    
    private $topicoNome;
    public function getTopicoNome() {
        return $this->topicoNome;
    }
    public function setTopicoNome($topicoNome) {
        $this->topicoNome = $topicoNome;
    }
    
    private $topicoOculto;
    public function getTopicoOculto()
    {
        return $this->topicoOculto;
    }
    public function setTopicoOculto($topicoOculto)
    {
        $this->topicoOculto = $topicoOculto;
    }
    
}//end class

?>
