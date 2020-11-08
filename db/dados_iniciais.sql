INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (1,'Blob','tbl_blob','''');
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (2,'Booleano','tbl_boolean',NULL);
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (3,'Data','tbl_date','''');
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (4,'Número','tbl_integer',NULL);
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (5,'Número Preciso','tbl_float',NULL);
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (6,'Ref. Objeto','tbl_objref',NULL);
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (7,'String','tbl_string','''');
INSERT INTO tipodado (cod_tipodado,nome,tabela,delimitador) VALUES (8,'Texto Avanc.','tbl_text','''');

INSERT INTO `perfil` (`cod_perfil`, `nome`) VALUES (1, 'Administrador'),
(2, 'Editor'),
(3, 'Autor'),
(4, 'Restrito'),
(5, 'Militarizado'),
(6, '_Default');

INSERT INTO status (cod_status,nome) VALUES (1,'Privado');
INSERT INTO status (cod_status,nome) VALUES (2,'Publicado');
INSERT INTO status (cod_status,nome) VALUES (3,'Rejeitado');
INSERT INTO status (cod_status,nome) VALUES (4,'Submetido');

INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (1, 'Diretório', 'folder', 'Diretório que contém outros objetos, além de um texto descritivo. Útil para dividir uma seção em categorias. Normalmente é utilizado para novos items de menu.', 1, 1, 1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (2, 'Interlink', 'interlink', 'Link para objetos do portal', 1, 1, 0);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (3, 'Página', 'document', 'Documento em HTML.',1,1,1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (4, 'Imagem','imagem','Ilustração ou foto a ser exibida ou linkada na página (ex: fotos da galeria, ilustrações sobre artigo).',0,1,1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (5, 'Arquivo','arquivo','Arquivo que será disponibilizado no site para download (ex: documentos do Word, PDF, arquivos compactados,  etc).',0,1,1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (6, 'Link','link','Link para endereços externos ao portal.',0,1,0);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (10, 'Notícia','noticia','',1,1,1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (11, 'Portal Data','portaldata','',1,1,1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (12, 'Portal Data Folder','portaldatafolder','',1,1,1);
INSERT INTO classe (cod_classe, nome, prefixo, descricao, temfilhos, sistema, indexar) VALUES (13, 'Agência de Notícias','agenciadenoticias','Área para criação e gerenciamento de notícias.',1,1,1);


INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (1,3,8,0,'0','texto',5,'Campo de texto da classe Documento','Texto','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (2,1,8,0,'0','texto',2,'','Texto','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (3,4,1,0,'0','conteudo',0,'Arquivo de imagem para upload. Localiza na máquina local.','Imagem','Sim','Não',1,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (4,5,1,0,'0','conteudo',0,'Arquivo a disponibilizar para upload.','Arquivo','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (5,4,7,0,'0','credito',1,'Nome do autor da imagem','Crédito','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (6,6,7,0,'0','descricao',2,'Descrição do link','Descrição','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (7,4,7,0,'0','legenda',2,'Legenda da imagem','Legenda','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (8,10,8,0,'','texto',2,'Texto da Notícia','Texto','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (9,10,2,0,'','destaque',7,'Definir se a notícia em causa é destaque ou não','Destacar?(S/N)','Sim','Não',0,3,'0');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (10,10,7,0,'','subtitulo',0,'Subtitulo da Notícia','Sub-Titulo','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (11,10,7,0,'','veiculo',4,'Veiculo onde a notícia foi vinculada','Veiculo','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (12,10,7,0,'','autor',5,'Autor da notícia','Autor','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (13,10,7,0,'','link',6,'Link para notícia original','Link para notícia original','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (14,1,2,0,'','menu_proprio',1,'Checa se este folder é inicio de seção e contém menu próprio ou se pega o menu do pai','Tem Menu Próprio?','Sim','Não',0,2,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (15,2,4,0,'','endereco',1,'Número do Objeto','Código do Objeto','Sim','Não',1,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (16,10,8,0,'','resumo',1,'Resumo da noticia','Resumo','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (17,3,8,0,'','resumo',1,'Resumo do Documento','Resumo','Sim','Não',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (18,6,7,0,'','endereco',1,'Endereço da URL a linkar neste objeto','Endereço Completo','Sim','Não',1,3,'http://');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (19,5,8,0,'','resumo',1,'Resumo do Arquivo','Resumo','','',0,3,'');
INSERT INTO propriedade (cod_propriedade,cod_classe,cod_tipodado,cod_referencia_classe,campo_ref,nome,posicao,descricao,rotulo,rot1booleano,rot2booleano,obrigatorio,seguranca,valorpadrao) VALUES (20,2,7,0,'','ancora',2,'','Linkar com ancora','','',0,3,'');

INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (11,12);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (13,10);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (10,5);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (3,5);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (10,6);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (10,4);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (3,6);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (3,3);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (3,4);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (10,2);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (3,2);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (1,5);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (1,1);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (1,4);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (1,2);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (1,6);
INSERT INTO classexfilhos (cod_classe,cod_classe_filho) VALUES (1,3);

INSERT INTO objeto (cod_objeto,cod_pai,cod_classe,cod_usuario,cod_pele,cod_status,titulo,descricao,data_publicacao,data_validade,script_exibir,apagado,objetosistema,peso) VALUES (1,-1,1,1,0,2,'Página Inicial','Página principal',19811212000000,20361212000000,'',0,1,1);