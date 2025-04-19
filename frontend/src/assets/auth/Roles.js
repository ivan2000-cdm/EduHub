const roles = {
  // Основные роли пользователей в системе
  ADMIN: 'ROLE_ADMIN',         // Администратор (директор, завуч, секретарь)
  TEACHER: 'ROLE_TEACHER',     // Преподаватель
  STUDENT: 'ROLE_STUDENT',     // Ученик
  PARENT: 'ROLE_PARENT',       // Родитель (опционально)

  // Группы ролей (используются для проверки доступа к разделам и функциям)
  SCHOOL_MANAGEMENT_SET: [],   // Роли, относящиеся к управлению школой
  TEACHER_SET: [],             // Все роли преподавателей
  STUDENT_SET: [],             // Все роли учеников
  PARENT_SET: []               // Все роли родителей
}

// Заполнение групп ролей

// Администрация школы (директор, завуч, секретарь)
roles.SCHOOL_MANAGEMENT_SET = [
  roles.ADMIN
]

// Преподаватели
roles.TEACHER_SET = [
  roles.TEACHER
]

// Ученики
roles.STUDENT_SET = [
  roles.STUDENT
]

// Родители
roles.PARENT_SET = [
  roles.PARENT
]

export default roles
