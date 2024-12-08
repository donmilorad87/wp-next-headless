"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useState } from "react";
import { DefaultValues, FieldValues, Path, SubmitHandler, useForm } from "react-hook-form";
import { z, ZodType } from "zod";

import { Input } from "../ui/input";

import { Button } from "@/components/ui/button";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { useToast } from "@/hooks/use-toast";
import { User } from "@/lib/users";


interface SalaryFormProps<T extends FieldValues> {
    schema: ZodType<T>;
    defaultValues: T;
    users: User[]
    bearer: string
}

interface SalaryReport {
    message: string;
    success: boolean;
    average_salary?: number;
    average_salary_eur?: number;
    condition?: string;
    currency?: string;
    currency_rate_to_eur?: number;
    code: number;
}
interface SlaryApiResponse {
    success: boolean;
    data: SalaryReport
}
const SalaryForm = <T extends FieldValues>({ schema, defaultValues, users, bearer }: SalaryFormProps<T>) => {


    const { toast } = useToast();

    const form = useForm<z.infer<typeof schema>>({
        resolver: zodResolver(schema),
        defaultValues: defaultValues as DefaultValues<T>,
    });

    const [message, setMessage] = useState<string>('');

    const handleSubmit: SubmitHandler<T> = async () => {
        console.log(form.getValues(), 'addsa');

        const myHeaders: Headers = new Headers();
        myHeaders.append("Content-Type", "application/json");
        myHeaders.append("Authorization", `Bearer ${bearer}`);

        const raw: string = JSON.stringify({
            date: form.getValues().date + '-01',
            salary: parseFloat(form.getValues().salary),
            user_id: parseInt(form.getValues().user_id),
        });

        const requestOptions: RequestInit = {
            method: "POST",
            headers: myHeaders,
            body: raw,
            redirect: "follow",
        };

        fetch("http://localhost/wp-json/gambling_api/v1/set_user_salary_for_mont_and_return_annual_by_full_name", requestOptions)
            .then((response) => response.json())
            .then((result: SlaryApiResponse) => {
                const data: SalaryReport = result.data;
                if (data.success === true && data.code === 777) {
                    toast({
                        variant: "default",
                        title: "Success",
                        description: "Salary added",
                    })
                    setMessage('Salary added');
                } else if (data.success === true && data.code === 888) {
                    toast({
                        variant: "default",
                        title: "Success",
                        description: `Salary added, average salary in country currency ammount: ${data.average_salary} ${data.currency} and in EUR: ${data.average_salary_eur}`,
                    })
                    setMessage(`Salary added, average salary in country currency ammount: ${data.average_salary} ${data.currency} and in EUR: ${data.average_salary_eur}`);
                }

                form.reset();

            })
            .catch((error) => console.error(error));
    };

    const buttonText = "Input salary";

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(handleSubmit)} className="mt-10 space-y-6 salaryForm">

                <FormField
                    key="user_id"
                    control={form.control}
                    name={"user_id" as Path<T>}
                    render={() => (
                        <FormItem className="flex w-full flex-col gap-2.5">
                            <FormLabel className="paragraph-medium text-dark200_light900">
                                Select user
                            </FormLabel>

                            <select className="w-full rounded border border-gray-300 px-3 py-2"
                                {...form.register("user_id" as Path<T>, { required: true })}>



                                {users.map((user: User) => (
                                    <option key={user.id} value={user.id}>
                                        {user.first_name} {user.last_name}
                                    </option>
                                ))}

                            </select>

                            <FormMessage />
                        </FormItem>
                    )}
                />
                <FormField
                    key="date"
                    control={form.control}
                    name={"date" as Path<T>}
                    render={() => (
                        <FormItem className="flex w-full flex-col gap-2.5">
                            <FormLabel className="paragraph-medium text-dark200_light900">
                                Start month:
                            </FormLabel>



                            <input type="month" id="start" min="2018-03" className="w-full rounded border border-gray-300 px-3 py-2" onKeyDown={(e) => e.preventDefault()}
                                {...form.register("date" as Path<T>, { required: true })} />

                            <FormMessage />
                        </FormItem>
                    )}
                />
                <FormField
                    key="salary"
                    control={form.control}
                    name={"salary" as Path<T>}
                    render={() => (
                        <FormItem className="flex w-full flex-col gap-2.5">
                            <FormLabel className="paragraph-medium text-dark200_light900">
                                Add Salary
                            </FormLabel>
                            <FormControl>
                                <Input
                                    required
                                    {...form.register("salary" as Path<T>, { required: true })}
                                    name={"salary" as Path<T>}
                                    type={"number"}

                                    className="w-full rounded border border-gray-300 px-3 py-2"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />
                <Button
                    disabled={form.formState.isSubmitting}
                    className="primary-gradient paragraph-medium rounded-2 font-inter !text-light-900 min-h-12 w-full px-4 py-3"
                >
                    {form.formState.isSubmitting ? "Loading..." : buttonText}
                </Button>
            </form>

            {message && <h3>{message}</h3>}
        </Form>
    );
};

export default SalaryForm;
