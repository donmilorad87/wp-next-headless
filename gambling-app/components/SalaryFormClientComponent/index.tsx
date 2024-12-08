"use client";
import React from 'react'

import SalaryForm from '../forms/SalaryForm';

import { User } from '@/lib/users'
import { SalarySchema } from '@/lib/validations';

const SalaryFormClientComponent = ({ users, bearer }: { users: User[], bearer: string }) => {
    return <SalaryForm schema={SalarySchema} defaultValues={{ user_id: "", date: "", salary: "" }} users={users} bearer={bearer} />
}

export default SalaryFormClientComponent