# Make Ordering - Form Plugin

![Joomla Badge](https://img.shields.io/badge/Joomla-5091CD?style=for-the-badge&logo=joomla&logoColor=white) ![PHP Badge](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

<div align="center">
  <img src="./.github/jlowcodelogo.png" width="350" />
</div>

## Conteúdo

- [Sobre](#sobre)
- [Configuração](#configuração)
  - [Order element](#order-element)
  - [Base element](#base-element)
- [Uso](#configuração)

## 💭 Sobre

O make_ordering é um plugin de formulário que permite a ordenação dos artigos em ordem alfabética por meio das seções.

## ⚙️ Configuração

De início, é necessário adicionar na lista um novo elemmento do tipo field que será responsável por armazenar a ordem atual:

![New element type field](./.github/01.png)

Agora é necessário editar o formulário, acessar a aba de plugins e selecionar o make_ordering como plugin.

Em condition, escreve a condição a qual o plugin será acionado, neste caso será como `return true`:

![return true condition](./.github/02.png)

### Order element

Na opção Order element, selecione a o element recém criado anteriormente que armazenará a ordem:

![Order element](./.github/03.png)

### Base element

Selecione o element que de fato será utilizado como base para realizar a ordenação

![Base element](./.github/04.png)

## 💻 Uso

Ao criar um novo registro ou editar um registro existente, o plugin irá realizar a primeira ordenação dos registros.
