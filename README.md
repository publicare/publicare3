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
Os metadados são:
- titulo
- descricao
- data_publicacao
- data_validade
- peso
- cod_pele
- cod_classe
- prefixo_classe 

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

Template, ou script de exibição, é o modelo de exibição de um objeto.
Define onde e como as propriedades serão exibidas.
Cada classe tem o seu próprio template.
Todo portal criado com o Publicare tem um template padrão, o ```<portal_root>/html/template/view_basic.php```, utilizado para exibir todas as propriedades do objeto.









### Markdown

Markdown is a lightweight and easy-to-use syntax for styling your writing. It includes conventions for

```markdown
Syntax highlighted code block

# Header 1
## Header 2
### Header 3

- Bulleted
- List

1. Numbered
2. List

**Bold** and _Italic_ and `Code` text

[Link](url) and ![Image](src)
```

For more details see [GitHub Flavored Markdown](https://guides.github.com/features/mastering-markdown/).

### Jekyll Themes

Your Pages site will use the layout and styles from the Jekyll theme you have selected in your [repository settings](https://github.com/publicare/publicare3/settings). The name of this theme is saved in the Jekyll `_config.yml` configuration file.

### Support or Contact

Having trouble with Pages? Check out our [documentation](https://help.github.com/categories/github-pages-basics/) or [contact support](https://github.com/contact) and we’ll help you sort it out.
