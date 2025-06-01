CREATE DATABASE TCC_Connect;

USE TCC_Connect;

CREATE TABLE Usuario (
    idUsuario INT AUTO_INCREMENT,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(80) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipoUsuario ENUM('Aluno', 'Ex-aluno', 'Mentor', 'Patrocinador') NOT NULL,
    status ENUM('Ativo', 'Inativo') DEFAULT 'Ativo',
    PRIMARY KEY (idUsuario),
    UNIQUE (email)
);

CREATE TABLE Estudante (
    idEstudante INT AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    curso VARCHAR(80) NOT NULL,
    anoConclusao YEAR NOT NULL,
    status ENUM('Cursando', 'Formado') NOT NULL,
    PRIMARY KEY (idEstudante),
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Mentor (
    idMentor INT AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    areaEspecialidade VARCHAR(100) NOT NULL,
    experiencia TEXT NOT NULL,
    PRIMARY KEY (idMentor),
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Patrocinador (
    idPatrocinador INT AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    empresa VARCHAR(80) NOT NULL,
    areaInteresse VARCHAR(100) NOT NULL,
    PRIMARY KEY (idPatrocinador),
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE TCC (
    idTCC INT AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('Em desenvolvimento', 'Concluído') NOT NULL,
    idAutor INT NOT NULL,
    anexo VARCHAR(255) NULL,
    PRIMARY KEY (idTCC),
    FOREIGN KEY (idAutor) REFERENCES Estudante(idEstudante) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Mentoria (
    idMentoria INT AUTO_INCREMENT,
    idEstudante INT NOT NULL,
    idMentor INT NOT NULL,
    statusMentoria ENUM('Solicitada', 'Em andamento', 'Concluída') NOT NULL,
    PRIMARY KEY (idMentoria),
    FOREIGN KEY (idEstudante) REFERENCES Estudante(idEstudante) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idMentor) REFERENCES Mentor(idMentor) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Patrocinio (
    idPatrocinio INT AUTO_INCREMENT,
    idTCC INT NOT NULL,
    idPatrocinador INT NOT NULL,
    valorPatrocinio DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (idPatrocinio),
    FOREIGN KEY (idTCC) REFERENCES TCC(idTCC) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idPatrocinador) REFERENCES Patrocinador(idPatrocinador) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Feedback (
    idFeedback INT AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    texto TEXT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idMentor INT NULL,
    idPatrocinador INT NULL,
    PRIMARY KEY (idFeedback),
    FOREIGN KEY (idMentor) REFERENCES Mentor(idMentor) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (idPatrocinador) REFERENCES Patrocinador(idPatrocinador) ON DELETE SET NULL ON UPDATE CASCADE
);

DELIMITER //
CREATE TRIGGER check_feedback_autor BEFORE INSERT ON Feedback
FOR EACH ROW
BEGIN
    IF NEW.idMentor IS NULL AND NEW.idPatrocinador IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Erro: O Feedback deve ter um Mentor ou um Patrocinador como autor.';
    END IF;
END //
DELIMITER ;

ALTER TABLE Feedback ADD COLUMN idTCC INT NOT NULL,
ADD FOREIGN KEY (idTCC) REFERENCES TCC(idTCC) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE Usuario ADD COLUMN foto VARCHAR(255);

ALTER TABLE Mentor RENAME COLUMN experiencia TO descricaoPerfil;

ALTER TABLE Patrocinador ADD COLUMN descricaoPerfil TEXT;

ALTER TABLE TCC RENAME COLUMN anexo TO link;