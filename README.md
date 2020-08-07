# Publicare 3

O Publicare é um Sistema Gerenciador de Conteúdo livre e de código aberto, utilizado na criação e gerenciamento de conteúdos de sites dinâmicos.  
Ideal para websites e portais com grande volume de conteúdo, onde existem inúmeros departamentos envolvidos com a tarefa de administração de conteúdos na Internet.

O conteúdo do website pode ser modificado de forma rápida e segura por usuários em diversos locais.

O conteúdo de uma página é inserido através de um editor próprio e mostrado através de moldes (templates) pré definidos do site.  
Isto resulta em um estilo corporativo mais consistente.  
Assim, até mesmo o número de pessoas produzindo páginas para publicação direta pode ser grande, a consistência de estilo, e o mais importante, a consistência na estrutura do conteúdo estão garantidas.

### O que é um Sistema de Gerenciamento de Conteúdo?
Um gerenciador de conteúdo é uma ferramenta que permite integrar e automatizar todos os processos relacionados à criação, personalização, controle de acesso e disponibilização de conteúdos em portais web.

Entende-se aqui por conteúdo não somente as informações que estão estruturadas nos bancos de dados da organização, como também aquelas não ou semi-estruturadas, não se limitando apenas a textos HTML, mas também áudio, vídeo, etc.

## Conceitos

### Classe

É um modelo ou especificação que define o tipo de objeto a ser criado. Através da definição de uma classe, descreve-se que propriedades o objeto terá.
As classes são usadas para criar objetos.

### Metadados

São os dados que todos os objetos possuem, independente da classe que pertencem.

São informações que ficam guardadas na tabela de objetos.

Os metadados são:

#### titulo
O título do objeto

#### descricao
Descrição do objeto. Normalmente usado para metatag description. Até 200 caracteres. 

#### data_publicacao
Data a partir do qual o objeto ficará visível para os usuários.

#### data_validade
Data a partir do qual o objeto ficará invisível para os usuários do site.

#### peso
Utilizado normalmente para ordenar os objetos através do comando localizar.

#### cod_pele
O código da pele a qual o objeto pertence. Caso nenhuma pele seja definida utiliza a pele padrão.

#### cod_classe
O código da classe ao qual o objeto pertence

#### prefixo_classe 
O prefixo da classe do objeto.

### Objeto

Um objeto nada mais é do que uma página que possui atributos distintos, herdados através da classe escolhida na hora de sua criação.  
Um objeto pode conter outros objetos, sendo estes chamados de objetos filhos.

### Objeto Pai

O objeto no qual outro objeto reside.  
Um objeto pai implica relação.  
Por exemplo, uma pasta é um objeto pai no qual um arquivo, ou objeto filho, reside.  

Um objeto pode ser um objeto pai e também um objeto filho.  
Por exemplo, uma subpasta que contém arquivos é a pasta filho da pasta pai e a pasta pai dos arquivos.

### Objeto Filho

Objeto que reside em outro objeto.  
Um objeto filho implica relação.  
Por exemplo, um arquivo é um objeto filho que reside em uma pasta, que é o objeto pai. 

### Pele

Pele é um conjunto de templates com header e footer próprios.

Ao ser aplicado a um objeto, todos os filhos recebem a mesma pele, recursivamente.

### Template

Template, ou script de exibição, é o modelo de exibição de um objeto. Define onde e como as propriedades e metadados serão exibidos.

Cada classe tem o seu próprio template.  
Ao renderizar um objeto, o publicare verifica a qual classe pertence o objeto e procura o template correspondente, se não encontrar o arquivo correspondente renderiza usando o template ```view_basic.php```.

Todo portal criado com o Publicare tem um template padrão, o ```<portal_root>/html/template/view_basic.php```, utilizado para exibir todas as propriedades do objeto.

Os Templates ficam na pasta do portal /html/template. São arquivos PHP, que possibilitam mesclar a execução da linguagem PBL e PHP. 

## A linguagem PBL

Foi desenvolvida a linguagem PBL para facilitar o consumo das informações no banco de dados, é uma linguagem interpretada, passando por uma transformação para PHP. Feita para renderizar as respostas parao navegador.

Possibilita o desenvolvimento de um portal sem ter conhecimentos de PHP ou SQL.

Funciona com as tags especiais ```<@``` e ```@>```

### Variaveis PBL

### Comandos PBL

#### eco
Imprime um valor na posição.

 **Utilização**:  
```<@eco {variavel|string|dado|numero|macro} @>```

#### ecoe
Imprime um valor na posição, aplicando a função htmlentities() do PHP.

**Utilização**:  
```<@ecoe {variavel|string|dado|numero|macro} @>```

#### eco_limite
Imprime um valor na posição, garantindo que o tamanho do texto não seja maior do que o informado.  
Não corta palavras ao meio.

**Utilização**:  
```<@eco_limite texto={variavel|string|dado|numero|macro} limite={variavel|numero}  @>```

#### var
Define valor de variáveis.  
Funciona para variáveis PBL e variáveis PHP.

**Utilização**:  
```<@var variavel={variavel|string|dado|numero|macro} @>```

#### se
Estrutura de condição do PBL. Equivalente ao ```if``` do PHP.  
Deve ser fechado com o comando **/se**.

**Utilização**:  
```<@se [{variavel|string|dado|numero}{>|<|<=|>=|==|!=}{variavel|string|dado|numero|macro}] @>```

**Fechamento**:  
```<@/se@>```   

#### senao
Estrutura de condição do PBL. Equivalente ao ```else``` do PHP.  
Deve ficar entre a abertura **se** e o fechamento **/se**.

**Utilização**:  
```<@senao@>```                    

#### repetir
Executa instruções de forma repetida.  
Equivalente à função ```for``` do PHP.

**Utilização**:  
```<@repetir {variavel}={numero inicial},{numero final}@>``` 

**Fechamento**:  
```<@/repetir@>``` 

#### filhos
Recupera lista de objetos filhos do objeto renderizado, gera um laço com o resultado.  
O bloco de código que ficar entre a abertura e fechamento deste comando será executado para cada objeto do conjunto retornado.

**Utilização**:  
```<@filhos nome=[{variavel}] classes=[{string}] ordem=[{string}]@>``` 

**Fechamento**:  
```<@/filhos@>``` 

#### semfilhos
Indica bloco que será executado caso o comando **filhos** retorne cnjunto vazio de objetos.  
Deve ser utilizado logo após o fechamento do comando **filhos**.

**Utilização**:  
```<@semfilhos@>``` 

**Fechamento**:  
```<@/semfilhos@>``` 
