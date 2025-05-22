SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `cupons` (
  `id` int NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `valor_desconto` decimal(10,2) NOT NULL,
  `validade` date NOT NULL,
  `valor_minimo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



CREATE TABLE `estoque` (
  `id` int NOT NULL,
  `produto_id` int NOT NULL,
  `variacao` varchar(100) DEFAULT NULL,
  `quantidade` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `pedidos` (
  `id` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `frete` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



CREATE TABLE `pedido_itens` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `produtos` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);


ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);


ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `cupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `pedido_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `estoque`
  ADD CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);
COMMIT;
