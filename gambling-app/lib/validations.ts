import { z } from "zod";

export const SignInSchema = z.object({
  username: z.string().min(1, { message: "Username is required." }),
  password: z.string().min(1, { message: "Password is required." }),
});


export const SalarySchema = z.object({
  user_id: z.string().min(1, { message: "User ID is required." }),
  date: z.string().min(1, { message: "Date is required." }),
  salary: z.string().min(1, { message: "Salary is required." }),
});

export const ProposeSchema = z.object({
  user_id: z.string().min(1, { message: "User ID is required." }),
});