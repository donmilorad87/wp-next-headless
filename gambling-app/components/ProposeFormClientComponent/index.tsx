"use client";
import React from 'react'

import ProposeForm from '../forms/ProposeForm';

import { User } from '@/lib/users'
import { ProposeSchema } from '@/lib/validations';

const ProposeFormClientComponent = ({ users, bearer }: { users: User[], bearer: string }) => {
    return <ProposeForm schema={ProposeSchema} defaultValues={{ user_id: "" }} users={users} bearer={bearer} />;
}

export default ProposeFormClientComponent